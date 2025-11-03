<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlanResource;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PlanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:patient']);
        $this->middleware('permission:patient.plans.index')->only(['index']);
        $this->middleware('permission:patient.plans.show')->only(['show']);
        $this->middleware('permission:patient.plans.record-adherence')->only(['recordAdherence']);
        $this->middleware('permission:patient.plans.adherence-stats')->only(['getAdherenceStats']);
    }
    /**
     * Display a listing of plans assigned to the authenticated patient.
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        
        $query = Plan::query()
            ->with([
                'planType',
                'elements.food.foodGroup',
                'elements.food.unit',
                'elements.exercise.exerciseType'
            ])
            ->where('patient_id', $currentUser->id);

        // Filtro de búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por tipo de plan
        if ($request->filled('plan_type_id')) {
            $query->where('plan_type_id', $request->plan_type_id);
        }

        // Ordenamiento
        $query->orderBy('created_at', 'desc');

        // Paginación con filas configurables
        $perPage = $request->filled('rows') ? (int)$request->rows : 12;
        $plans = $query->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Patient/Plans/Index', [
            'plans' => PlanResource::collection($plans),
            'filters' => $request->only(['search', 'rows', 'status', 'plan_type_id']),
        ]);
    }

    /**
     * Display the specified plan.
     */
    public function show(Plan $plan)
    {
        // Verificar que el plan pertenece al paciente logueado
        if ($plan->patient_id !== Auth::user()->id) {
            abort(403, 'No tienes permisos para ver este plan.');
        }

        $plan->load([
            'planType',
            'patient:id,name,last_name,second_last_name,email',
            'elements.food.foodGroup',
            'elements.food.unit',
            'elements.exercise.exerciseType'
        ]);

        return Inertia::render('Patient/Plans/Show', [
            'plan' => new PlanResource($plan),
        ]);
    }

    /**
     * Record daily adherence for the authenticated patient's plan.
     */
    public function recordAdherence(Plan $plan, Request $request)
    {
        // Verificar que el plan pertenece al paciente logueado
        if ($plan->patient_id !== Auth::user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para modificar este plan.'
            ], 403);
        }

        // Verificar que el plan está activo
        if (!$plan->isCurrentlyActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Este plan no está activo actualmente.'
            ], 400);
        }

        // Verificar si ya se registró adherencia hoy
        if (!$plan->shouldBeTrackedToday()) {
            return response()->json([
                'success' => false,
                'message' => 'La adherencia de hoy ya fue registrada.'
            ], 400);
        }

        try {
            // Registrar la adherencia del día
            $plan->recordTrackedDay();
            
            // Recargar el plan para obtener los datos actualizados
            $plan->refresh();

            return response()->json([
                'success' => true,
                'message' => '¡Excelente! Has completado tu plan del día.',
                'data' => [
                    'adherence_percentage' => $plan->overall_adherence,
                    'adherence_status' => $plan->adherence_status_spanish,
                    'days_tracked' => $plan->days_tracked,
                    'total_plan_days' => $plan->total_plan_days,
                    'last_tracked_date' => $plan->last_tracked_date?->format('Y-m-d'),
                    'should_track_today' => $plan->shouldBeTrackedToday(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la adherencia. Intenta nuevamente.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get adherence statistics for the authenticated patient's plan.
     */
    public function getAdherenceStats(Plan $plan)
    {
        // Verificar que el plan pertenece al paciente logueado
        if ($plan->patient_id !== Auth::user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para ver este plan.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'plan_id' => $plan->id,
                'title' => $plan->title,
                'adherence_percentage' => $plan->overall_adherence,
                'adherence_status' => $plan->adherence_status_spanish,
                'adherence_status_code' => $plan->adherence_status,
                'days_tracked' => $plan->days_tracked,
                'total_plan_days' => $plan->total_plan_days,
                'days_remaining' => $plan->total_plan_days - $plan->days_tracked,
                'should_track_today' => $plan->shouldBeTrackedToday(),
                'last_tracked_date' => $plan->last_tracked_date?->format('d/m/Y'),
                'is_currently_active' => $plan->isCurrentlyActive(),
                'vigency_status' => $plan->vigency_status,
                'start_date' => $plan->start_date->format('d/m/Y'),
                'end_date' => $plan->end_date->format('d/m/Y'),
            ]
        ]);
    }
}