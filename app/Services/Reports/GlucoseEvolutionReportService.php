<?php

namespace App\Services\Reports;

use App\Models\Patient\Measure;
use App\Models\Patient\MeasureType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GlucoseEvolutionReportService
{
    public function generate(array $filters = [])
    {
        $query = $this->getBaseQuery($filters);
        $measures = $query->clone()
            ->orderBy('measured_at', 'asc')
            ->orderBy('hour_measured', 'asc')
            ->get();
        $stats = $this->getStatsData($query);

        return [
            'chartData'   => $this->formatChartData($measures),
            'tableData'   => $this->formatTableData($measures),
            'stats'       => $this->formatStatsCards($stats),
            'filters'     => $this->getFiltersDefinition($filters),
            'reportType'  => 'glucose-evolution',
            'reportTitle' => 'De evolución de glucosa'
        ];
    }

    private function getBaseQuery(array $filters)
    {
        $query = Measure::query()
            ->with([
                'measureConfig.patient:id,name,last_name,second_last_name,email',
                'measureConfig.measureType:id,name,unit'
            ])
            ->whereHas('measureConfig.measureType', function($q) {
                $q->where('name', 'LIKE', '%glucosa%')
                  ->orWhere('name', 'LIKE', '%glucose%');
            });

        // Filtro por rango de fechas
        $query->when($filters['start_date'] ?? null, function ($q, $date) {
            $q->where('measured_at', '>=', Carbon::parse($date)->startOfDay());
        });

        $query->when($filters['end_date'] ?? null, function ($q, $date) {
            $q->where('measured_at', '<=', Carbon::parse($date)->endOfDay());
        });

        // Filtro por paciente específico
        $query->when($filters['patient_id'] ?? null, function ($q, $patientId) {
            $q->whereHas('measureConfig', function($subQuery) use ($patientId) {
                $subQuery->where('patient_id', $patientId);
            });
        });

        // Filtro por contexto (ayuno/posprandial) basado en las notas
        $query->when($filters['context'] ?? null, function ($q, $context) {
            if ($context === 'ayuno') {
                $q->where(function($subQ) {
                    $subQ->where('notes', 'LIKE', '%ayuno%')
                         ->orWhere('notes', 'LIKE', '%ayunas%')
                         ->orWhere('notes', 'LIKE', '%fasting%');
                });
            } elseif ($context === 'posprandial') {
                $q->where(function($subQ) {
                    $subQ->where('notes', 'LIKE', '%posprandial%')
                         ->orWhere('notes', 'LIKE', '%postprandial%')
                         ->orWhere('notes', 'LIKE', '%después%')
                         ->orWhere('notes', 'LIKE', '%comida%');
                });
            }
        });

        return $query;
    }

    private function getStatsData($query)
    {
        $stats = $query->select(
            DB::raw('AVG(value) as avg_glucose'),
            DB::raw('MAX(value) as max_glucose'),
            DB::raw('MIN(value) as min_glucose'),
            DB::raw('COUNT(id) as total_measures'),
            DB::raw('COUNT(DISTINCT DATE(measured_at)) as days_with_measures'),
            // Conteo por niveles de glucosa (mg/dL)
            DB::raw('SUM(CASE WHEN value < 70 THEN 1 ELSE 0 END) as hypoglycemia'), // Hipoglucemia
            DB::raw('SUM(CASE WHEN value >= 70 AND value <= 100 THEN 1 ELSE 0 END) as normal_fasting'), // Normal ayuno
            DB::raw('SUM(CASE WHEN value >= 101 AND value <= 125 THEN 1 ELSE 0 END) as prediabetes'), // Prediabetes
            DB::raw('SUM(CASE WHEN value > 125 THEN 1 ELSE 0 END) as diabetes_range'), // Rango diabético
            // Estadísticas por contexto
            DB::raw('COUNT(CASE WHEN notes LIKE "%ayuno%" OR notes LIKE "%ayunas%" THEN 1 END) as fasting_measures'),
            DB::raw('COUNT(CASE WHEN notes LIKE "%posprandial%" OR notes LIKE "%postprandial%" THEN 1 END) as postprandial_measures')
        )->first();

        return $stats;
    }

    private function formatStatsCards($stats)
    {
        $avgGlucose = round($stats->avg_glucose ?? 0, 1);
        $totalMeasures = number_format($stats->total_measures ?? 0);
        $daysWithMeasures = number_format($stats->days_with_measures ?? 0);
        
        // Porcentaje de mediciones en rango normal (70-100 mg/dL para ayuno)
        $normalPercent = $stats->total_measures > 0 
            ? round(($stats->normal_fasting / $stats->total_measures) * 100, 1)
            : 0;

        return [
            ['label' => 'Glucosa promedio', 'value' => $avgGlucose . ' mg/dL'],
            ['label' => 'Total mediciones', 'value' => $totalMeasures],
            ['label' => 'Días con mediciones', 'value' => $daysWithMeasures],
            ['label' => 'Mediciones normales', 'value' => $normalPercent . '%'],
        ];
    }

    private function formatChartData($measures)
    {
        // Agrupar mediciones por fecha para crear la línea de tiempo
        $groupedData = $measures->groupBy(function($measure) {
            return Carbon::parse($measure->measured_at)->format('Y-m-d');
        });

        $labels = [];
        $glucoseData = [];

        foreach ($groupedData as $date => $dayMeasures) {
            $labels[] = Carbon::parse($date)->format('d M');
            
            // Promedio del día
            $dailyAvg = $dayMeasures->avg('value');
            $glucoseData[] = round($dailyAvg, 1);
        }

        return [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Glucosa (mg/dL)',
                        'type' => 'line',
                        'data' => $glucoseData,
                        'borderColor' => '#EF4444',
                        'backgroundColor' => 'transparent',
                        'borderWidth' => 2,
                        'fill' => false,
                        'tension' => 0.0,
                        'pointRadius' => 4,
                        'pointHoverRadius' => 6,
                        'pointBackgroundColor' => '#EF4444',
                        'pointBorderColor' => '#EF4444',
                        'pointBorderWidth' => 0,
                    ]
                ]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'elements' => [
                    'line' => [
                        'tension' => 0
                    ],
                    'point' => [
                        'radius' => 4,
                        'hoverRadius' => 6
                    ]
                ],
                'plugins' => [
                    'legend' => [
                        'display' => true,
                        'position' => 'top',
                    ],
                    'tooltip' => [
                        'mode' => 'index',
                        'intersect' => false,
                    ]
                ],
                'interaction' => [
                    'mode' => 'point',
                    'intersect' => true
                ],
                'scales' => [
                    'x' => [
                        'type' => 'category',
                        'display' => true,
                        'title' => [
                            'display' => true,
                            'text' => 'Fecha'
                        ],
                        'grid' => [
                            'display' => true
                        ]
                    ],
                    'y' => [
                        'type' => 'linear',
                        'display' => true,
                        'position' => 'left',
                        'title' => [
                            'display' => true,
                            'text' => 'Glucosa (mg/dL)'
                        ],
                        'min' => 50,
                        'max' => 200,
                        'grid' => [
                            'display' => true
                        ]
                    ]
                ]
            ]
        ];
    }

    private function formatTableData($measures)
    {
        return [
            'headers' => [
                ['key' => 'date', 'label' => 'Fecha'],
                ['key' => 'time', 'label' => 'Hora'],
                ['key' => 'patient', 'label' => 'Paciente'],
                ['key' => 'glucose_value', 'label' => 'Valor (mg/dL)'],
                ['key' => 'level', 'label' => 'Nivel'],
                ['key' => 'context', 'label' => 'Contexto'],
                ['key' => 'notes', 'label' => 'Observaciones'],
            ],
            'rows' => $measures->map(function ($measure) {
                $patientName = trim(
                    ($measure->measureConfig->patient->name ?? '') . ' ' . 
                    ($measure->measureConfig->patient->last_name ?? '') . ' ' . 
                    ($measure->measureConfig->patient->second_last_name ?? '')
                );

                $glucoseLevel = $this->getGlucoseLevel($measure->value);
                $context = $this->extractContext($measure->notes ?? '');
                
                return [
                    'date' => Carbon::parse($measure->measured_at)->format('d M Y'),
                    'time' => $measure->hour_measured ?? 'No especificada',
                    'patient' => $patientName ?: 'N/A',
                    'glucose_value' => $measure->value . ' mg/dL',
                    'level' => $glucoseLevel,
                    'context' => $context,
                    'notes' => $measure->notes ? (strlen($measure->notes) > 50 ? substr($measure->notes, 0, 47) . '...' : $measure->notes) : 'Sin observaciones',
                ];
            })
        ];
    }

    private function getGlucoseLevel($value)
    {
        if ($value < 70) return 'Hipoglucemia';
        if ($value <= 100) return 'Normal';
        if ($value <= 125) return 'Prediabetes';
        return 'Diabetes';
    }

    private function extractContext($notes)
    {
        $notes = strtolower($notes);
        
        if (strpos($notes, 'ayuno') !== false || strpos($notes, 'ayunas') !== false) {
            return 'Ayuno';
        }
        
        if (strpos($notes, 'posprandial') !== false || 
            strpos($notes, 'postprandial') !== false ||
            strpos($notes, 'después') !== false ||
            strpos($notes, 'comida') !== false) {
            return 'Posprandial';
        }
        
        return 'No especificado';
    }

    private function getFiltersDefinition(array $currentFilters)
    {
        return [
            [
                'name' => 'start_date',
                'label' => 'Fecha desde',
                'type' => 'date',
                'value' => $currentFilters['start_date'] ?? null
            ],
            [
                'name' => 'end_date',
                'label' => 'Fecha hasta',
                'type' => 'date',
                'value' => $currentFilters['end_date'] ?? null
            ],
            [
                'name' => 'patient_id',
                'label' => 'Paciente',
                'type' => 'select',
                'value' => $currentFilters['patient_id'] ?? null,
                'options' => User::role('patient')
                    ->select(['id', DB::raw("CONCAT(name, ' ', COALESCE(last_name, '')) as name")])
                    ->get()
            ],
            [
                'name' => 'context',
                'label' => 'Contexto',
                'type' => 'select',
                'value' => $currentFilters['context'] ?? null,
                'options' => [
                    ['id' => 'ayuno', 'name' => 'Ayuno'],
                    ['id' => 'posprandial', 'name' => 'Posprandial'],
                ]
            ],
        ];
    }
}