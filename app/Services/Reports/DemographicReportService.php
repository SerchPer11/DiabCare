<?php

namespace App\Services\Reports;

use App\Models\Patient\PatientPathologies;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class DemographicReportService
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
            'reportType'  => 'demographic',
            'reportTitle' => 'Demografico',
        ];
    }

    private function getBaseQuery(array $filters)
    {
        $query = User::role('patient');

        $query->when($filters['start_date'] ?? null, function ($q, $date) {
            $q->where('created_at', '>=', Carbon::parse($date)->startOfDay());
        });

        $query->when($filters['end_date'] ?? null, function ($q, $date) {
            $q->where('created_at', '<=', Carbon::parse($date)->endOfDay());
        });

        $query->when($filters['gender_id'] ?? null, function ($q, $gender) {
            $q->where('gender', $gender);
        });

        return $query;
    }

    private function getStatsData($query)
    {
        $statsQuery = $query->clone();
        $ageSql = "TIMESTAMPDIFF(YEAR, birthdate, CURDATE())";
        $stats = $statsQuery->select(
            DB::raw('COUNT(id) as total_records'),
            DB::raw("SUM(CASE WHEN gender = 'male' THEN 1 ELSE 0 END) as count_male"),
            DB::raw("SUM(CASE WHEN gender = 'female' THEN 1 ELSE 0 END) as count_female"),
            DB::raw("SUM(CASE WHEN gender = 'other' THEN 1 ELSE 0 END) as count_others"),
            DB::raw("SUM(CASE WHEN ($ageSql) <= 17 THEN 1 ELSE 0 END) as count_0_17"),
            DB::raw("SUM(CASE WHEN ($ageSql) BETWEEN 18 AND 29 THEN 1 ELSE 0 END) as count_18_29"),
            DB::raw("SUM(CASE WHEN ($ageSql) BETWEEN 30 AND 44 THEN 1 ELSE 0 END) as count_30_44"),
            DB::raw("SUM(CASE WHEN ($ageSql) BETWEEN 45 AND 59 THEN 1 ELSE 0 END) as count_45_59"),
            DB::raw("SUM(CASE WHEN ($ageSql) BETWEEN 60 AND 74 THEN 1 ELSE 0 END) as count_60_74"),
            DB::raw("SUM(CASE WHEN ($ageSql) >= 75 THEN 1 ELSE 0 END) as count_75_plus")
        )->first();

        return $stats;
    }

    private function formatStatsCards($stats)
    {
        $total = $stats->total_records;

        if ($total == 0) {
            return [
                ['label' => 'Población total', 'value' => 0],
                ['label' => 'Mujeres', 'value' => '0% (0)'],
                ['label' => 'Hombres', 'value' => '0% (0)'],
                ['label' => 'Otro(s)', 'value' => '0% (0)'],
            ];
        }

        $percent_male = round(($stats->count_male / $total) * 100);
        $percent_female = round(($stats->count_female / $total) * 100);
        $percent_others = round(($stats->count_others / $total) * 100);

        $format_total = number_format($total);
        $format_male = number_format($stats->count_male);
        $format_female = number_format($stats->count_female);
        $format_others = number_format($stats->count_others);

        return [
            ['label' => 'Población total', 'value' => $format_total],
            ['label' => 'Mujeres', 'value' => "{$percent_female}% ({$format_female})"],
            ['label' => 'Hombres', 'value' => "{$percent_male}% ({$format_male})"],
            ['label' => 'Otro(s)', 'value' => "{$percent_others}% ({$format_others})"],
        ];
    }

    private function formatChartData($stats)
    {
        $labels = [
            '0-17',
            '18-29',
            '30-44',
            '45-59',
            '60-74',
            '75+'
        ];

        $data = [
            $stats->count_0_17,
            $stats->count_18_29,
            $stats->count_30_44,
            $stats->count_45_59,
            $stats->count_60_74,
            $stats->count_75_plus,
        ];

        $barColor = '#208B8B';

        return [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => 'Distribución por grupo de edad',
                    'data' => $data,
                    'backgroundColor' => $barColor,
                    'borderColor' => $barColor,
                    'borderWidth' => 1,
                    'borderRadius' => 6,
                ]]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'display' => false
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Gráfica de barras: Distribución por grupo de edad'
                    ]
                ],
                'scales' => [
                    'y' => [ 
                        'display' => false,
                        'grid' => [
                            'display' => false 
                        ]
                    ],
                    'x' => [ // Eje X
                        'grid' => [
                            'display' => false
                        ]
                    ]
                ]
            ]
        ];
    }

    private function formatTableData($readings)
    {
        $options = [
            'male' => 'Hombre',
            'female' => 'Mujer',
            'other' => 'Otro',
        ];
        return [
            'headers' => [
                ['key' => 'created_at', 'label' => 'Fecha'],
                ['key' => 'patient', 'label' => 'Paciente'],
                ['key' => 'value', 'label' => 'Género'],
                ['key' => 'age', 'label' => 'Edad']
            ],
            'rows' => $readings->map(function ($reading) use ($options) {
                return [
                    'created_at' => Carbon::parse($reading->created_at)->format('d M Y, h:ia'),
                    'patient' => $reading->name . ' ' . $reading->last_name ?? 'N/A',
                    'value' => $options[$reading->gender] ?? 'No aplica',
                    'age' => $reading->birthdate ? Carbon::parse($reading->birthdate)->age . ' años' : 'N/A'
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
        ];
    }
}
