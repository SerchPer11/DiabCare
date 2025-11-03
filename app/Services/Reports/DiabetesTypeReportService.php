<?php

namespace App\Services\Reports;

use App\Models\Patient\Measure;
use App\Models\Patient\MeasureType;
use App\Models\Patient\PatientPathologies;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class DiabetesTypeReportService
{
    public function generate(array $filters = [])
    {
        $query = $this->getBaseQuery($filters);
        $readings = $query->clone()
            ->orderBy('created_at', 'asc') // Importante para gráficas de tiempo
            ->get();
        $stats = $this->getStatsData($query);

        return [
            'chartData'   => $this->formatChartData($stats),
            'tableData'   => $this->formatTableData($readings),
            'stats'       => $this->formatStatsCards($stats),
            'filters'     => $this->getFiltersDefinition($filters),
            'reportType'  => 'diabetes-type',
            'reportTitle' => 'De tipos de diabetes'
        ];
    }

    private function getBaseQuery(array $filters)
    {
        $query = PatientPathologies::query()->with('patientProfile.user');

        $query->when($filters['start_date'] ?? null, function ($q, $date) {
            $q->where('created_at', '>=', Carbon::parse($date)->startOfDay());
        });

        $query->when($filters['end_date'] ?? null, function ($q, $date) {
            $q->where('created_at', '<=', Carbon::parse($date)->endOfDay());
        });

        $query->when($filters['type_id'] ?? null, function ($q, $type) {
            $q->where('diabetes_type', $type);
        });

        $query->when($filters['gender_id'] ?? null, function ($q, $gender) {
            $q->whereHas('patientProfile.user', function ($mq) use ($gender) {
                $mq->where('gender', $gender);
            });
        });

        return $query;
    }

    private function getStatsData($query)
    {
        $statsQuery = $query->clone();

        $stats = $statsQuery->select(
            DB::raw('COUNT(id) as total_records'),
            DB::raw("SUM(CASE WHEN diabetes_type = 'TP1' THEN 1 ELSE 0 END) as count_tp1"),
            DB::raw("SUM(CASE WHEN diabetes_type = 'TP2' THEN 1 ELSE 0 END) as count_tp2"),
            DB::raw("SUM(CASE 
                        WHEN diabetes_type = 'TP1' THEN 0
                        WHEN diabetes_type = 'TP2' THEN 0
                        WHEN diabetes_type = 'Gest' THEN 1
                    END) as count_gest"),
            DB::raw("SUM(CASE 
                        WHEN diabetes_type = 'TP1' THEN 0
                        WHEN diabetes_type = 'TP2' THEN 0
                        WHEN diabetes_type = 'Gest' THEN 0
                        ELSE 1
                    END) as count_otros")
        )->first();

        return $stats;
    }

    private function formatStatsCards($stats)
    {
        $total = $stats->total_records;

        if ($total == 0) {
            return [
                ['label' => 'Total pacientes', 'value' => 0],
                ['label' => 'Tipo 2', 'value' => '0% (0)'],
                ['label' => 'Tipo 1', 'value' => '0% (0)'],
                ['label' => 'Gestacional', 'value' => '0% (0)'],
            ];
        }

        $percent_tp1 = round(($stats->count_tp1 / $total) * 100);
        $percent_tp2 = round(($stats->count_tp2 / $total) * 100);
        $percent_gest = round(($stats->count_gest / $total) * 100);
        $percent_otros = round(($stats->count_otros / $total) * 100);

        $format_total = number_format($total);
        $format_tp1 = number_format($stats->count_tp1);
        $format_tp2 = number_format($stats->count_tp2);
        $format_gest = number_format($stats->count_gest);
        $format_otros = number_format($stats->count_otros);

        return [
            ['label' => 'Total pacientes', 'value' => $format_total],
            ['label' => 'Tipo 2', 'value' => "{$percent_tp2}% ({$format_tp2})"],
            ['label' => 'Tipo 1', 'value' => "{$percent_tp1}% ({$format_tp1})"],
            ['label' => 'Gestacional', 'value' => "{$percent_gest}% ({$format_gest})"],
        ];
    }

    private function formatChartData($stats)
    {
        $labels = [
            'Tipo 1',
            'Tipo 2',
            'Gestacional',
            'N/A'
        ];
        $data = [
            $stats->count_tp1,
            $stats->count_tp2,
            $stats->count_gest,
            $stats->count_otros
        ];
        $colors = [
            '#FF6384',
            '#36A2EB',
            '#FFCE56',
            '#6156f7ff'
        ];
        return [
            'type' => 'pie',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => 'Distribución de Pacientes',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => '#ffffff',
                    'borderWidth' => 2,
                ]]
            ],
            'options' => [ // Puedes pre-configurar opciones de Chart.js
                'responsive' => true,
                'maintainAspectRatio' => false,
            ]
        ];
    }

    private function formatTableData($readings)
    {
        //dd($readings);
        $options = [
            'TP1' => 'Tipo 1',
            'TP2' => 'Tipo 2',
            'Gest' => 'Gestacional',
        ];
        return [
            'headers' => [
                ['key' => 'created_at', 'label' => 'Fecha'],
                ['key' => 'patient', 'label' => 'Paciente'],
                ['key' => 'value', 'label' => 'Tipo de Diabetes'],
            ],
            'rows' => $readings->map(function ($reading) use ($options) {
                return [
                    'created_at' => Carbon::parse($reading->created_at)->format('d M Y, h:ia'),
                    'patient' => $reading->patientProfile->user->name . ' ' . $reading->patientProfile->user->last_name ?? 'N/A',
                    'value' => $options[$reading->diabetes_type] ?? 'No aplica',
                ];
            })
        ];
    }

    private function getFiltersDefinition(array $currentFilters)
    {
        return [
            [
                'name' => 'start_date',
                'label' => 'Desde',
                'type' => 'date',
                'value' => $currentFilters['start_date'] ?? null
            ],
            [
                'name' => 'end_date',
                'label' => 'Hasta',
                'type' => 'date',
                'value' => $currentFilters['end_date'] ?? null
            ],
            [
                'name' => 'gender_id',
                'label' => 'Genero',
                'type' => 'select',
                'value' => $currentFilters['gender_id'] ?? null,
                'options' => [
                    ['id' => 'male', 'name' => 'Masculino'],
                    ['id' => 'female', 'name' => 'Femenino'],
                    ['id' => 'other', 'name' => 'Otro']
                ],
            ],
            [
                'name' => 'type_id',
                'label' => 'Tipo de Diabetes',
                'type' => 'select',
                'value' => $currentFilters['type_id'] ?? null,
                'options' => [
                    ['id' => 'TP1', 'name' => 'Tipo 1'],
                    ['id' => 'TP2', 'name' => 'Tipo 2'],
                    ['id' => 'Gest', 'name' => 'Gestacional']
                ],
            ],
        ];
    }
}
