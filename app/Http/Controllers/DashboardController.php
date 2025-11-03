<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Backup;
use App\Models\Patient\Measure;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct()
    {
        // Middleware manejado en routes
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $data = [];

        try {
            if ($user->hasRole('admin')) {
                $data = $this->getAdminDashboardData();
            } elseif ($user->hasRole('doctor')) {
                $data = $this->getDoctorDashboardData($user);
            } elseif ($user->hasRole('patient')) {
                $data = $this->getPatientDashboardData($user);
            }

            return Inertia::render('Dashboard', [
                'dashboardData' => $data,
                'userRole' => $user->roles->first()->name ?? 'user'
            ]);
        } catch (\Exception $e) {
            // En caso de error, mostrar dashboard básico
            return Inertia::render('Dashboard', [
                'dashboardData' => [
                    'type' => 'basic',
                    'error' => $e->getMessage()
                ],
                'userRole' => $user->roles->first()->name ?? 'user'
            ]);
        }
    }

    /**
     * Get dashboard data for admin users
     */
    private function getAdminDashboardData(): array
    {
        // 1. Nuevos usuarios de la semana
        $newUsersThisWeek = User::where('created_at', '>=', Carbon::now()->startOfWeek())
            ->where('created_at', '<=', Carbon::now()->endOfWeek())
            ->count();

        // 2. Total de usuarios en el sistema
        $totalUsers = User::count();

        // 3. Fecha/hora del último respaldo
        $lastBackup = Backup::where('status', 'completed')
            ->latest('completed_at')
            ->first();

        // 4. Gráfica de altas por semana (últimas 4 semanas)
        $weeklyRegistrations = [];
        for ($i = 3; $i >= 0; $i--) {
            $startOfWeek = Carbon::now()->subWeeks($i)->startOfWeek();
            $endOfWeek = Carbon::now()->subWeeks($i)->endOfWeek();
            
            $count = User::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
            
            $weeklyRegistrations[] = [
                'week' => $startOfWeek->format('M d') . ' - ' . $endOfWeek->format('M d'),
                'count' => $count,
                'date' => $startOfWeek->format('Y-m-d')
            ];
        }

        // 5. Lista de los 5 usuarios más recientes
        $recentUsers = User::with('roles')
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name . ' ' . $user->last_name,
                    'email' => $user->email,
                    'role' => $user->roles->first()->description ?? 'Sin rol',
                    'created_at' => $user->created_at->format('d/m/Y H:i'),
                    'created_at_human' => $user->created_at->diffForHumans()
                ];
            });

        return [
            'type' => 'admin',
            'indicators' => [
                'newUsersThisWeek' => $newUsersThisWeek,
                'totalUsers' => $totalUsers,
                'lastBackup' => [
                    'date' => $lastBackup?->completed_at?->format('d/m/Y'),
                    'time' => $lastBackup?->completed_at?->format('H:i'),
                    'formatted' => $lastBackup?->completed_at?->format('d/m/Y H:i') ?? 'Sin respaldos',
                    'human' => $lastBackup?->completed_at?->diffForHumans() ?? 'Nunca'
                ]
            ],
            'weeklyRegistrations' => $weeklyRegistrations,
            'recentUsers' => $recentUsers,
            'quickActions' => [
                [
                    'title' => 'Crear Usuario',
                    'description' => 'Registrar un nuevo usuario en el sistema',
                    'route' => 'users.create',
                    'icon' => 'user-plus',
                    'color' => 'blue'
                ],
                [
                    'title' => 'Gestionar Roles',
                    'description' => 'Administrar roles y permisos del sistema',
                    'route' => 'roles.index',
                    'icon' => 'shield',
                    'color' => 'green'
                ],
                [
                    'title' => 'Módulo de Respaldos',
                    'description' => 'Gestionar respaldos del sistema',
                    'route' => 'backups.index',
                    'icon' => 'database',
                    'color' => 'purple'
                ]
            ]
        ];
    }

    /**
     * Get dashboard data for doctor users
     */
    private function getDoctorDashboardData(User $doctor): array
    {
        // 1. Citas de hoy
        $todayAppointments = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('date', Carbon::today())
            ->whereHas('status', function($query) {
                $query->where('name', 'programada');
            })
            ->count();

        // 2. Pacientes en seguimiento activo
        $activePatientsCount = Plan::where('assigned_by', $doctor->id)
            ->where('status', 'activo')
            ->where('end_date', '>=', Carbon::today())
            ->distinct('patient_id')
            ->count('patient_id');

        // 3. Planes por vencer en los próximos 7 días
        $plansExpiringCount = Plan::where('assigned_by', $doctor->id)
            ->where('status', 'activo')
            ->whereBetween('end_date', [Carbon::today(), Carbon::today()->addDays(7)])
            ->count();

        // 4. Lista de las próximas 5 citas
        $upcomingAppointments = Appointment::where('doctor_id', $doctor->id)
            ->with(['patient', 'status'])
            ->whereDate('date', '>=', Carbon::today())
            ->whereHas('status', function($query) {
                $query->where('name', 'programada');
            })
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->limit(5)
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'patient_name' => $appointment->patient->name . ' ' . $appointment->patient->last_name,
                    'date' => Carbon::parse($appointment->date)->format('d/m/Y'),
                    'time' => Carbon::parse($appointment->time)->format('H:i'),
                    'modality' => $appointment->modality,
                    'reason' => $appointment->reason,
                    'is_today' => Carbon::parse($appointment->date)->isToday(),
                    'is_tomorrow' => Carbon::parse($appointment->date)->isTomorrow()
                ];
            });

        return [
            'type' => 'doctor',
            'indicators' => [
                'todayAppointments' => $todayAppointments,
                'activePatientsCount' => $activePatientsCount,
                'plansExpiringCount' => $plansExpiringCount
            ],
            'upcomingAppointments' => $upcomingAppointments,
            'quickActions' => [
                [
                    'title' => 'Agendar Cita',
                    'description' => 'Programar una nueva cita médica',
                    'route' => 'doctor.appointments.create',
                    'icon' => 'calendar-plus',
                    'color' => 'blue'
                ],
                [
                    'title' => 'Asignar Plan',
                    'description' => 'Crear un nuevo plan de alimentación y actividad',
                    'route' => 'doctor.plans.create',
                    'icon' => 'clipboard-list',
                    'color' => 'green'
                ],
                [
                    'title' => 'Seguimientos clinicos',
                    'description' => 'Ver y gestionar pacientes en seguimiento',
                    'route' => 'patients.index',
                    'icon' => 'file-text',
                    'color' => 'purple'
                ]
            ]
        ];
    }

    /**
     * Get dashboard data for patient users
     */
    private function getPatientDashboardData(User $patient): array
    {
        // 1. Próxima cita
        $nextAppointment = Appointment::where('patient_id', $patient->id)
            ->with(['doctor', 'status'])
            ->whereDate('date', '>=', Carbon::today())
            ->whereHas('status', function($query) {
                $query->where('name', 'programada');
            })
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->first();

        // 2. Porcentaje de metas cumplidas en la semana (sistema mejorado)
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        // A. Adherencia a planes activos esta semana
        $activePlans = Plan::where('patient_id', $patient->id)
            ->where('status', 'activo')
            ->where('start_date', '<=', $endOfWeek)
            ->where('end_date', '>=', $startOfWeek)
            ->get();
        
        $planAdherenceScore = 0;
        $totalPlanWeight = 0;
        
        foreach ($activePlans as $plan) {
            // Calcular días que el plan estuvo activo esta semana
            $planStart = max($plan->start_date, $startOfWeek);
            $planEnd = min($plan->end_date, $endOfWeek);
            $activeDaysThisWeek = $planStart->diffInDays($planEnd) + 1;
            
            // Calcular días completados esta semana
            $completedDaysThisWeek = 0;
            if ($plan->last_tracked_date && $plan->last_tracked_date->between($startOfWeek, $endOfWeek)) {
                // Estimar días completados basado en adherencia general y días activos
                $weeklyAdherence = ($plan->overall_adherence / 100) * $activeDaysThisWeek;
                $completedDaysThisWeek = min($activeDaysThisWeek, round($weeklyAdherence));
            }
            
            $planScore = $activeDaysThisWeek > 0 ? ($completedDaysThisWeek / $activeDaysThisWeek) * 100 : 0;
            $planAdherenceScore += $planScore * $activeDaysThisWeek; // Peso por días activos
            $totalPlanWeight += $activeDaysThisWeek;
        }
        
        // B. Adherencia a mediciones (peso menor)
        $measuresThisWeek = Measure::whereHas('measureConfig', function($query) use ($patient) {
                $query->where('patient_id', $patient->id);
            })
            ->whereBetween('measured_at', [$startOfWeek, $endOfWeek])
            ->count();
        
        $daysInWeek = min(7, Carbon::now()->diffInDays($startOfWeek) + 1); // No contar días futuros
        $expectedMeasures = $daysInWeek * 1; // 1 medición por día es más realista
        $measureScore = $expectedMeasures > 0 ? min(100, ($measuresThisWeek / $expectedMeasures) * 100) : 0;
        
        // C. Cálculo final combinado (70% planes, 30% mediciones)
        if ($totalPlanWeight > 0) {
            $avgPlanScore = $planAdherenceScore / $totalPlanWeight;
            $completionPercentage = ($avgPlanScore * 0.7) + ($measureScore * 0.3);
        } else {
            // Si no hay planes activos, solo usar mediciones
            $completionPercentage = $measureScore;
        }

        // 3. Planes activos
        $activePlansCount = Plan::where('patient_id', $patient->id)
            ->where('status', 'activo')
            ->where('end_date', '>=', Carbon::today())
            ->count();

        // 4. Gráfica de historial de glucosa (últimas 4 semanas)
        $glucoseHistory = [];
        for ($i = 3; $i >= 0; $i--) {
            $startOfWeek = Carbon::now()->subWeeks($i)->startOfWeek();
            $endOfWeek = Carbon::now()->subWeeks($i)->endOfWeek();
            
            $avgGlucose = Measure::whereHas('measureConfig', function($query) use ($patient) {
                    $query->where('patient_id', $patient->id)
                          ->whereHas('measureType', function($subQuery) {
                              $subQuery->where('name', 'LIKE', '%glucosa%')
                                       ->orWhere('name', 'LIKE', '%glucose%');
                          });
                })
                ->whereBetween('measured_at', [$startOfWeek, $endOfWeek])
                ->avg('value');
            
            $glucoseHistory[] = [
                'week' => $startOfWeek->format('M d'),
                'average' => $avgGlucose ? round($avgGlucose, 1) : 0,
                'date' => $startOfWeek->format('Y-m-d')
            ];
        }

        // 5. Tareas del día (basadas en planes activos)
        $todayTasks = Plan::where('patient_id', $patient->id)
            ->where('status', 'activo')
            ->where('start_date', '<=', Carbon::today())
            ->where('end_date', '>=', Carbon::today())
            ->with(['planType', 'elements'])
            ->limit(5)
            ->get()
            ->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'title' => $plan->title,
                    'type' => $plan->planType->name ?? 'Plan',
                    'description' => $plan->description,
                    'completed' => !$plan->shouldBeTrackedToday() // Si no debe trackearse hoy, significa que ya se completó
                ];
            });

        return [
            'type' => 'patient',
            'indicators' => [
                'nextAppointment' => $nextAppointment ? [
                    'date' => Carbon::parse($nextAppointment->date)->format('d/m/Y'),
                    'time' => Carbon::parse($nextAppointment->time)->format('H:i'),
                    'doctor' => $nextAppointment->doctor->name . ' ' . $nextAppointment->doctor->last_name,
                    'modality' => $nextAppointment->modality,
                    'days_until' => Carbon::parse($nextAppointment->date)->diffInDays(Carbon::today()),
                    'is_today' => Carbon::parse($nextAppointment->date)->isToday(),
                    'is_tomorrow' => Carbon::parse($nextAppointment->date)->isTomorrow()
                ] : null,
                'weeklyGoalCompletion' => round($completionPercentage, 1),
                'activePlansCount' => $activePlansCount
            ],
            'glucoseHistory' => $glucoseHistory,
            'todayTasks' => $todayTasks,
            'quickActions' => [
                [
                    'title' => 'Registrar Medición',
                    'description' => 'Anotar una nueva medición de glucosa',
                    'route' => 'measures.create',
                    'icon' => 'activity',
                    'color' => 'red'
                ],
                [
                    'title' => 'Ver Plan del Día',
                    'description' => 'Revisar mi plan de alimentación y ejercicio',
                    'route' => 'patient.plans.index',
                    'icon' => 'calendar-check',
                    'color' => 'green'
                ],
                [
                    'title' => 'Mi Historial Médico',
                    'description' => 'Ver mi historial y seguimiento clínico',
                    'route' => 'patient.medical-history.index',
                    'icon' => 'calendar',
                    'color' => 'blue'
                ]
            ]
        ];
    }
}