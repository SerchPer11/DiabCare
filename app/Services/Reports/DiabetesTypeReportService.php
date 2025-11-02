<?php

namespace App\Services\Reports;

use App\Models\Patient\Measure;
use App\Models\Patient\MeasureType;
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
        $stats = $this->getStats($query);

        return [
            'reportTitle' => 'Reporte de Niveles de Glucosa',
            'chartData'   => $this->formatChartData($readings),
            'tableData'   => $this->formatTableData($readings),
            'stats'       => $stats,
            'filters'     => $this->getFiltersDefinition($filters),
            'reportType'  => 'diabetes-type',
            'reportTitle' => 'De mediciones registradas'
        ];
    }

    private function getBaseQuery(array $filters)
    {
        $query = Measure::query()->with('measureConfig.patient', 'measureConfig.measureType');

        $query->when($filters['start_date'] ?? null, function ($q, $date) {
            $q->where('created_at', '>=', Carbon::parse($date)->startOfDay());
        });

        $query->when($filters['end_date'] ?? null, function ($q, $date) {
            $q->where('created_at', '<=', Carbon::parse($date)->endOfDay());
        });

        $query->when($filters['measure_type_id'] ?? null, function ($q, $patientId) {
            $q->whereHas('measureConfig', function ($mq) use ($patientId) {
                $mq->where('measure_type_id', $patientId);
            });
        });

        $query->when($filters['patient_id'] ?? null, function ($q, $patientId) {
            $q->whereHas('measureConfig', function ($mq) use ($patientId) {
                $mq->where('patient_id', $patientId);
            });
        });

        return $query;
    }

    private function getStats($query)
    {
        $stats = $query->select(
            DB::raw('AVG(value) as average'),
            DB::raw('MAX(value) as max'),
            DB::raw('MIN(value) as min'),
            DB::raw('COUNT(id) as total_records')
        )->first(); 
        
        return [
            ['label' => 'Promedio', 'value' => round($stats->average, 2)],
            ['label' => 'Nivel Máximo', 'value' => $stats->max],
            ['label' => 'Nivel Mínimo', 'value' => $stats->min],
            ['label' => 'Eventos Registrados', 'value' => $stats->total_records],
        ];
    }

    private function formatChartData($readings)
    {
        return [
            'type' => 'bar', // Tipo de gráfica (bar, line, pie, etc.)
            'data' => [
                'labels' => $readings->pluck('created_at')->map(fn($date) => Carbon::parse($date)->format('d/m/Y')),
                'datasets' => [[
                    'label' => 'Medida',
                    'data' => $readings->pluck('value'),
                    'backgroundColor' => '#7CB9EE', // Un color "medic"
                    'borderColor' => '#D6E8FB',
                    'borderWidth' => 1,
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
        return [
            'headers' => [
                ['key' => 'created_at', 'label' => 'Fecha'],
                ['key' => 'patient', 'label' => 'Paciente'],
                ['key' => 'value', 'label' => 'Nivel Registrado'],
                ['key' => 'notes', 'label' => 'Observaciones'],
            ],
            'rows' => $readings->map(function ($reading) {
                return [
                    'created_at' => Carbon::parse($reading->created_at)->format('d M Y, h:ia'),
                    'patient' => $reading->measureConfig->patient->name . ' ' . $reading->measureConfig->patient->last_name ?? 'N/A', // Usamos la relación
                    'value' => $reading->value,
                    'notes' => $reading->notes ?? '-',
                ];
            })
        ];
    }

    /**
     * HELPER 5: DEFINE QUÉ FILTROS MOSTRAR EN VUE
     * Esto le dice a tu componente Vue qué filtros debe renderizar.
     */
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
                'name' => 'measure_type_id',
                'label' => 'Tipo',
                'type' => 'select',
                'options' => MeasureType::select(['id', 'name'])->get(),
            ],
            [
                'name' => 'patient_id',
                'label' => 'Paciente',
                'type' => 'select',
                'value' => $currentFilters['patient_id'] ?? null,
                // Le pasamos las opciones al 'select'
                'options' => User::role('patient')->select(['id', DB::raw("CONCAT(name, ' ', last_name) as name")])->get()
            ],
        ];
    }
}
