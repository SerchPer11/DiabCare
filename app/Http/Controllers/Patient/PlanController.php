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
}