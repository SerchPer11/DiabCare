<?php

namespace App\Services\Reports;

use App\Models\Plan;
use App\Models\PlanType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PlanAdherenceReportService
{
    public function generate(array $filters = [])
    {
        $query = $this->getBaseQuery($filters);
        $plans = $query->clone()
            ->orderBy('overall_adherence', 'desc')
            ->get();
        $stats = $this->getStatsData($query);

        return [
            'chartData'   => $this->formatChartData($stats),
            'tableData'   => $this->formatTableData($plans),
            'stats'       => $this->formatStatsCards($stats),
            'filters'     => $this->getFiltersDefinition($filters),
            'reportType'  => 'plan-adherence',
            'reportTitle' => 'De adherencia a planes'
        ];
    }

    private function getBaseQuery(array $filters)
    {
        $query = Plan::query()
            ->with([
                'patient:id,name,last_name,second_last_name,email',
                'planType:id,name,description',
                'assignedBy:id,name,last_name'
            ]);

        // Filtro por rango de fechas de inicio del plan
        $query->when($filters['start_date'] ?? null, function ($q, $date) {
            $q->where('start_date', '>=', Carbon::parse($date)->startOfDay());
        });

        $query->when($filters['end_date'] ?? null, function ($q, $date) {
            $q->where('start_date', '<=', Carbon::parse($date)->endOfDay());
        });

        // Filtro por tipo de plan
        $query->when($filters['plan_type_id'] ?? null, function ($q, $typeId) {
            $q->where('plan_type_id', $typeId);
        });

        // Filtro por paciente
        $query->when($filters['patient_id'] ?? null, function ($q, $patientId) {
            $q->where('patient_id', $patientId);
        });

        // Filtro por estado del plan
        $query->when($filters['status'] ?? null, function ($q, $status) {
            $q->where('status', $status);
        });

        return $query;
    }

    private function getStatsData($query)
    {
        $stats = $query->select(
            DB::raw('AVG(overall_adherence) as avg_adherence'),
            DB::raw('MAX(overall_adherence) as max_adherence'),
            DB::raw('MIN(overall_adherence) as min_adherence'),
            DB::raw('COUNT(id) as total_plans'),
            DB::raw('SUM(CASE WHEN overall_adherence >= 80 THEN 1 ELSE 0 END) as high_adherence'),
            DB::raw('SUM(CASE WHEN overall_adherence >= 60 AND overall_adherence < 80 THEN 1 ELSE 0 END) as medium_adherence'),
            DB::raw('SUM(CASE WHEN overall_adherence >= 40 AND overall_adherence < 60 THEN 1 ELSE 0 END) as low_adherence'),
            DB::raw('SUM(CASE WHEN overall_adherence < 40 THEN 1 ELSE 0 END) as very_low_adherence'),
            DB::raw('AVG(total_plan_days) as avg_plan_duration'),
            DB::raw('AVG(days_tracked) as avg_days_tracked')
        )->first();

        return $stats;
    }

    private function formatStatsCards($stats)
    {
        $avgAdherence = round($stats->avg_adherence ?? 0, 1);
        $totalPlans = number_format($stats->total_plans ?? 0);
        $highAdherence = number_format($stats->high_adherence ?? 0);
        $avgDuration = round($stats->avg_plan_duration ?? 0, 1);

        $highAdherencePercent = $stats->total_plans > 0 
            ? round(($stats->high_adherence / $stats->total_plans) * 100, 1)
            : 0;

        return [
            ['label' => 'Adherencia promedio', 'value' => $avgAdherence . '%'],
            ['label' => 'Total de planes', 'value' => $totalPlans],
            ['label' => 'Adherencia alta (≥80%)', 'value' => "{$highAdherencePercent}% ({$highAdherence})"],
            ['label' => 'Duración promedio', 'value' => $avgDuration . ' días'],
        ];
    }

    private function formatChartData($stats)
    {
        $labels = [
            'Alta (≥80%)',
            'Media (60-79%)',
            'Baja (40-59%)',
            'Muy baja (<40%)'
        ];

        $data = [
            $stats->high_adherence ?? 0,
            $stats->medium_adherence ?? 0,
            $stats->low_adherence ?? 0,
            $stats->very_low_adherence ?? 0
        ];

        $colors = [
            '#10B981', // Verde - Alta adherencia
            '#F59E0B', // Amarillo - Media adherencia  
            '#F97316', // Naranja - Baja adherencia
            '#EF4444'  // Rojo - Muy baja adherencia
        ];

        return [
            'type' => 'doughnut',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => 'Distribución de Adherencia',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => '#ffffff',
                    'borderWidth' => 2,
                ]]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'position' => 'bottom'
                    ]
                ]
            ]
        ];
    }

    private function formatTableData($plans)
    {
        return [
            'headers' => [
                ['key' => 'patient', 'label' => 'Paciente'],
                ['key' => 'plan_title', 'label' => 'Plan'],
                ['key' => 'plan_type', 'label' => 'Tipo'],
                ['key' => 'adherence', 'label' => 'Adherencia'],
                ['key' => 'days_info', 'label' => 'Días Seguidos/Total'],
                ['key' => 'duration', 'label' => 'Duración'],
                ['key' => 'status', 'label' => 'Estado'],
                ['key' => 'last_tracked', 'label' => 'Último Seguimiento'],
            ],
            'rows' => $plans->map(function ($plan) {
                $patientName = trim(
                    ($plan->patient->name ?? '') . ' ' . 
                    ($plan->patient->last_name ?? '') . ' ' . 
                    ($plan->patient->second_last_name ?? '')
                );

                $planTypeName = $plan->planType->name === 'alimentacion' 
                    ? 'Alimentación' 
                    : ($plan->planType->name === 'actividad_fisica' ? 'Actividad Física' : $plan->planType->name);

                $adherenceLevel = $this->getAdherenceLevel($plan->overall_adherence);
                
                $daysInfo = "{$plan->days_tracked}/{$plan->total_plan_days}";
                
                $duration = Carbon::parse($plan->start_date)->diffInDays(Carbon::parse($plan->end_date)) + 1;
                
                $lastTracked = $plan->last_tracked_date 
                    ? Carbon::parse($plan->last_tracked_date)->format('d M Y')
                    : 'Sin seguimiento';

                return [
                    'patient' => $patientName ?: 'N/A',
                    'plan_title' => $plan->title ?: 'Sin título',
                    'plan_type' => $planTypeName,
                    'adherence' => round($plan->overall_adherence, 1) . '% (' . $adherenceLevel . ')',
                    'days_info' => $daysInfo,
                    'duration' => $duration . ' días',
                    'status' => ucfirst($plan->status),
                    'last_tracked' => $lastTracked,
                ];
            })
        ];
    }

    private function getAdherenceLevel($adherence)
    {
        if ($adherence >= 80) return 'Alta';
        if ($adherence >= 60) return 'Media';
        if ($adherence >= 40) return 'Baja';
        return 'Muy baja';
    }

    private function getFiltersDefinition(array $currentFilters)
    {
        return [
            [
                'name' => 'start_date',
                'label' => 'Fecha inicio (desde)',
                'type' => 'date',
                'value' => $currentFilters['start_date'] ?? null
            ],
            [
                'name' => 'end_date',
                'label' => 'Fecha inicio (hasta)',
                'type' => 'date',
                'value' => $currentFilters['end_date'] ?? null
            ],
            [
                'name' => 'plan_type_id',
                'label' => 'Tipo de Plan',
                'type' => 'select',
                'value' => $currentFilters['plan_type_id'] ?? null,
                'options' => PlanType::select(['id', 'name'])->get()->map(function($type) {
                    return [
                        'id' => $type->id,
                        'name' => $type->name === 'alimentacion' 
                            ? 'Alimentación' 
                            : ($type->name === 'actividad_fisica' ? 'Actividad Física' : $type->name)
                    ];
                })
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
                'name' => 'status',
                'label' => 'Estado del Plan',
                'type' => 'select',
                'value' => $currentFilters['status'] ?? null,
                'options' => [
                    ['id' => 'activo', 'name' => 'Activo'],
                    ['id' => 'completado', 'name' => 'Completado'],
                    ['id' => 'cancelado', 'name' => 'Cancelado'],
                ]
            ],
        ];
    }
}