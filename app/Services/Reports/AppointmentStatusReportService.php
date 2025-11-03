<?php

namespace App\Services\Reports;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AppointmentStatusReportService
{
    public function generate(array $filters = [])
    {
        $query = $this->getBaseQuery($filters);

        $statsData = $this->getStatsData($query);

        $weeklyData = $this->getWeeklyData($query);

        $filtersDefinition = $this->getFiltersDefinition($filters);

        return [
            'chartData'   => $this->formatChartData($weeklyData),
            'tableData'   => $this->formatTableData($weeklyData),
            'stats'       => $this->formatStatsCards($statsData),
            'filters'     => $filtersDefinition,
            'reportType'  => 'appointment-status',
            'reportTitle' => 'Citas por estado y modalidad',
        ];
    }

    private function getBaseQuery(array $filters)
    {
        $query = Appointment::query()->with('status');

        $query->when($filters['start_date'] ?? null, function ($q, $date) {
            $q->where('created_at', '>=', Carbon::parse($date)->startOfDay());
        });

        $query->when($filters['end_date'] ?? null, function ($q, $date) {
            $q->where('created_at', '<=', Carbon::parse($date)->endOfDay());
        });

        $query->when($filters['doctor_id'] ?? null, function ($q, $doctorId) {
            $q->where('doctor_id', $doctorId);
        });

        $query->when($filters['modality_id'] ?? null, function ($q, $modalityId) {
            $q->where('modality', $modalityId);
        });

        return $query;
    }

    private function getStatsData($query)
    {
        $statsQuery = $query->clone();

        // !!! AJUSTA ESTOS IDs A TU BASE DE DATOS !!!
        $ID_COMPLETADA = 4;
        $ID_CANCELADA = 5;

        $stats = $statsQuery->select(
            DB::raw('COUNT(id) as total_citas'),

            DB::raw("SUM(CASE WHEN appointment_status_id = $ID_COMPLETADA THEN 1 ELSE 0 END) as count_completadas"),
            DB::raw("SUM(CASE WHEN appointment_status_id = $ID_CANCELADA THEN 1 ELSE 0 END) as count_canceladas"),

            DB::raw("SUM(CASE WHEN modality = 'virtual' THEN 1 ELSE 0 END) as count_virtual"),
            DB::raw("SUM(CASE WHEN modality = 'presencial' THEN 1 ELSE 0 END) as count_presencial")
        )->first();

        return $stats;
    }

    private function formatStatsCards($stats)
    {
        $total = $stats->total_citas;

        if ($total == 0) {
            return [
                ['label' => 'Total citas', 'value' => 0],
                ['label' => 'Completadas', 'value' => 0],
                ['label' => 'Canceladas', 'value' => 0],
                ['label' => 'Virtual vs Presencial', 'value' => '0% / 0%'],
            ];
        }

        $percent_virtual = round(($stats->count_virtual / $total) * 100);
        $percent_presencial = round(($stats->count_presencial / $total) * 100);

        return [
            ['label' => 'Total citas', 'value' => number_format($stats->total_citas)],
            ['label' => 'Completadas', 'value' => number_format($stats->count_completadas)],
            ['label' => 'Canceladas', 'value' => number_format($stats->count_canceladas)],
            ['label' => 'Virtual vs Presencial', 'value' => "{$percent_virtual}% / {$percent_presencial}%"],
        ];
    }

    private function getWeeklyData($query)
    {
        $weeklyQuery = $query->clone();

        $ID_COMPLETADA = 4;
        $ID_CANCELADA = 5;

        $weekExpression = DB::raw('YEARWEEK(date, 1) as week_identifier');

        $weeklyData = $weeklyQuery
            ->select(
                $weekExpression,
                DB::raw('MIN(date) as week_start'),
                DB::raw('MAX(date) as week_end'),

                DB::raw('COUNT(id) as count_agendadas'),
                DB::raw("SUM(CASE WHEN appointment_status_id = $ID_COMPLETADA THEN 1 ELSE 0 END) as count_completadas"),
                DB::raw("SUM(CASE WHEN appointment_status_id = $ID_CANCELADA THEN 1 ELSE 0 END) as count_canceladas"),

                DB::raw("SUM(CASE WHEN modality = 'virtual' THEN 1 ELSE 0 END) as count_virtual"),
                DB::raw("SUM(CASE WHEN modality = 'presencial' THEN 1 ELSE 0 END) as count_presencial")
            )
            ->groupBy('week_identifier')
            ->orderBy('week_identifier', 'desc')
            ->take(4)
            ->get();

        return $weeklyData;
    }

    private function formatChartData($weeklyData)
    {
        $sortedData = $weeklyData->reverse();

        $labels = $sortedData->map(fn($week) => 'Sem ' . intval(substr($week->week_identifier, 4)));
        $virtualData = $sortedData->pluck('count_virtual');
        $presencialData = $sortedData->pluck('count_presencial');

        $colorVirtual = '#208B8B';
        $colorPresencial = '#A0D9D9';

        return [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Virtual',
                        'data' => $virtualData,
                        'backgroundColor' => $colorVirtual,
                        'borderRadius' => 4,
                    ],
                    [
                        'label' => 'Presencial',
                        'data' => $presencialData,
                        'backgroundColor' => $colorPresencial,
                        'borderRadius' => 4,
                    ]
                ]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => ['position' => 'bottom', 'align' => 'start'],
                    'title' => ['display' => true, 'text' => 'Citas por estatus y modalidad']
                ],
                'scales' => [
                    'x' => ['grid' => ['display' => false]],
                    'y' => ['beginAtZero' => true, 'grid' => ['display' => true, 'borderDash' => [5, 5]]]
                ]
            ]
        ];
    }

    private function formatTableData($weeklyData)
    {
        return [
            'headers' => [
                ['key' => 'periodo', 'label' => 'Periodo'],
                ['key' => 'agendadas', 'label' => 'Agendadas'],
                ['key' => 'completadas', 'label' => 'Completadas'],
                ['key' => 'canceladas', 'label' => 'Canceladas'],
                ['key' => 'modalidad', 'label' => 'Modalidad (V/P)'],
            ],
            'rows' => $weeklyData->map(function ($week) {
                $total = $week->count_agendadas;

                if ($total == 0) {
                    $percent_virtual = 0;
                    $percent_presencial = 0;
                } else {
                    $percent_virtual = round(($week->count_virtual / $total) * 100);
                    $percent_presencial = round(($week->count_presencial / $total) * 100);
                }

                $weekNum = intval(substr($week->week_identifier, 4));
                $startDate = Carbon::parse($week->week_start)->format('d M');
                $endDate = Carbon::parse($week->week_end)->format('d M Y');

                return [
                    'periodo' => "Semana $weekNum ($startDate - $endDate)",
                    'agendadas' => $week->count_agendadas,
                    'completadas' => $week->count_completadas,
                    'canceladas' => $week->count_canceladas,
                    'modalidad' => "{$percent_virtual}% / {$percent_presencial}%",
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
                'name' => 'doctor_id',
                'label' => 'Doctor',
                'type' => 'select',
                'value' => $currentFilters['doctor_id'] ?? null,
                'options' => User::role('doctor')->select(['id', DB::raw("CONCAT(name, ' ', last_name) as name")])->get(),
            ],
            [
                'name' => 'modality_id',
                'label' => 'Modalidad',
                'type' => 'select',
                'value' => $currentFilters['modality_id'] ?? null,
                'options' => [
                    ['id' => 'Presencial', 'name' => 'Presencial'],
                    ['id' => 'Virtual', 'name' => 'Virtual']
                ],
            ],
        ];
    }
}
