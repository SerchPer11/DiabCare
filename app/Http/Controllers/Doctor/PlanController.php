<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PlanType;
use App\Models\PlanElement;
use App\Models\User;
use App\Models\Doctor\Catalogs\Food;
use App\Models\Doctor\Catalogs\Exercise;
use App\Http\Resources\PlanResource;
use App\Traits\Filterable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PlanController extends Controller
{
    use Filterable;

    protected $routeName = 'doctor.plans.';
    protected $source = 'Doctor/Plans/Pages/';

    /**
     * Display a listing of plans.
     */
    public function index(Request $request)
    {
        $filters = $this->getFiltersBase($request->query());

        $query = Plan::with(['patient', 'planType', 'assignedBy'])
            ->when($filters->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', '%' . $search . '%')
                        ->orWhereHas('patient', function ($query) use ($search) {
                            $query->where('name', 'LIKE', '%' . $search . '%')
                                ->orWhere('last_name', 'LIKE', '%' . $search . '%');
                        });
                });
            });

        $plans = $query->orderBy($filters->order ?? 'created_at', $filters->direction ?? 'desc')
            ->paginate($filters->rows)
            ->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'title' => 'Planes de Alimentación y Actividad',
            'plans' => PlanResource::collection($plans),
            'routeName' => $this->routeName,
            'filters' => (array) $filters
        ]);
    }

    /**
     * Show the form for creating a new plan.
     */
    public function create()
    {
        $patients = User::role('patient')
            ->select('id', 'name', 'last_name', 'second_last_name', 'email')
            ->orderBy('name')
            ->get();

        $planTypes = PlanType::active()
            ->select('id', 'name', 'description')
            ->get();

        $foods = Food::where('is_active', true)
            ->select('id', 'name', 'food_group_id', 'calories', 'protein', 'carbohydrates', 'fats', 'portion_size')
            ->with(['foodGroup:id,name', 'unit:id,name'])
            ->orderBy('name')
            ->get();

        $exercises = Exercise::where('is_active', true)
            ->select('id', 'name', 'exercise_type_id', 'intensity', 'duration_minutes', 'calories_burned', 'sets', 'repetitions', 'equipment', 'notes')
            ->with('exerciseType:id,name')
            ->orderBy('name')
            ->get();

        return Inertia::render("{$this->source}Create", [
            'title' => 'Crear Plan',
            'patients' => $patients,
            'planTypes' => $planTypes,
            'foods' => $foods,
            'exercises' => $exercises,
            'routeName' => $this->routeName,
        ]);
    }

    /**
     * Store a newly created plan.
     */
    public function store(Request $request)
    {
        
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'plan_type_id' => 'required|exists:plan_types,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'elements' => 'required|array|min:1',
            'elements.*.food_id' => 'nullable|exists:foods,id',
            'elements.*.exercise_id' => 'nullable|exists:exercises,id',
            'elements.*.frequency' => 'required|string|max:100',
            'elements.*.intensity' => 'nullable|string|in:baja,media,alta',
            'elements.*.quantity' => 'required|numeric|min:0',
            'elements.*.unit' => 'required|string|max:50',
            'elements.*.time_schedule' => 'required|string|max:100',
            'elements.*.instructions' => 'nullable|string',
            'elements.*.notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $plan = Plan::create([
                'patient_id' => $request->patient_id,
                'plan_type_id' => $request->plan_type_id,
                'assigned_by' => Auth::id(),
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => 'activo',
            ]);

            // Create plan elements
            foreach ($request->elements as $index => $element) {
                PlanElement::create([
                    'plan_id' => $plan->id,
                    'food_id' => $element['food_id'] ?? null,
                    'exercise_id' => $element['exercise_id'] ?? null,
                    'frequency' => $element['frequency'],
                    'intensity' => $element['intensity'] ?? null,
                    'quantity' => $element['quantity'],
                    'unit' => $element['unit'],
                    'time_schedule' => $element['time_schedule'],
                    'instructions' => $element['instructions'] ?? null,
                    'notes' => $element['notes'] ?? null,
                    'order' => $index + 1,
                ]);
            }

            DB::commit();

            // TODO: Send notification to patient

            return redirect()
                ->route("{$this->routeName}index")
                ->with('success', 'Plan creado exitosamente');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear el plan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified plan.
     */
    public function show(Plan $plan)
    {
        $plan->load(['patient', 'planType', 'assignedBy', 'activeElements.food.foodGroup', 'activeElements.exercise.exerciseType']);

        return Inertia::render("{$this->source}Show", [
            'title' => 'Detalle del Plan',
            'plan' => $plan,
            'routeName' => $this->routeName,
        ]);
    }

    /**
     * Show the form for editing the specified plan.
     */
    public function edit(Plan $plan)
    {
        $plan->load(['elements']);

        $patients = User::role('patient')
            ->select('id', 'name', 'last_name', 'second_last_name', 'email')
            ->orderBy('name')
            ->get();

        $planTypes = PlanType::active()
            ->select('id', 'name', 'description')
            ->get();

        $foods = Food::where('is_active', true)
            ->select('id', 'name', 'food_group_id', 'calories', 'protein', 'carbohydrates', 'fats', 'portion_size')
            ->with(['foodGroup:id,name', 'unit:id,name'])
            ->orderBy('name')
            ->get();

        $exercises = Exercise::where('is_active', true)
            ->select('id', 'name', 'exercise_type_id', 'intensity', 'duration_minutes', 'calories_burned', 'sets', 'repetitions', 'equipment', 'notes')
            ->with('exerciseType:id,name')
            ->orderBy('name')
            ->get();

        return Inertia::render("{$this->source}Edit", [
            'title' => 'Editar Plan',
            'plan' => new PlanResource($plan),
            'patients' => $patients,
            'planTypes' => $planTypes,
            'foods' => $foods,
            'exercises' => $exercises,
            'routeName' => $this->routeName,
        ]);
    }

    /**
     * Update the specified plan.
     */
    public function update(Request $request, Plan $plan)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'plan_type_id' => 'required|exists:plan_types,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'elements' => 'required|array|min:1',
            'elements.*.food_id' => 'nullable|exists:foods,id',
            'elements.*.exercise_id' => 'nullable|exists:exercises,id',
            'elements.*.frequency' => 'required|string',
            'elements.*.intensity' => 'nullable|string|in:baja,media,alta',
            'elements.*.quantity' => 'nullable|numeric|min:0',
            'elements.*.unit' => 'nullable|string',
            'elements.*.time_schedule' => 'required|string|max:100',
            'elements.*.notes' => 'nullable|string',
            'elements.*.instructions' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $plan->update([
                'patient_id' => $request->patient_id,
                'plan_type_id' => $request->plan_type_id,
                'assigned_by' => Auth::id(),
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);

            // Delete existing elements and recreate
            $plan->elements()->delete();

            // Create updated plan elements
            foreach ($request->elements as $index => $element) {
                PlanElement::create([
                    'plan_id' => $plan->id,
                    'food_id' => $element['food_id'] ?? null,
                    'exercise_id' => $element['exercise_id'] ?? null,
                    'frequency' => $element['frequency'],
                    'intensity' => $element['intensity'] ?? null,
                    'quantity' => $element['quantity'] ?? null,
                    'unit' => $element['unit'] ?? null,
                    'time_schedule' => $element['time_schedule'] ?? null,
                    'notes' => $element['notes'] ?? null,
                    'instructions' => $element['instructions'] ?? null,
                    'order' => $index + 1,
                ]);
            }

            DB::commit();

            return redirect()
                ->route("{$this->routeName}index")
                ->with('success', 'Plan actualizado exitosamente');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el plan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified plan from storage.
     */
    public function destroy(Plan $plan)
    {
        try {
            $plan->delete();

            return redirect()
                ->route("{$this->routeName}index")
                ->with('success', 'Plan eliminado exitosamente');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al eliminar el plan: ' . $e->getMessage());
        }
    }

    /**
     * Duplicate an existing plan for reuse.
     */
    public function duplicate(Plan $plan)
    {
        try {
            DB::beginTransaction();

            // Create a copy of the plan
            $newPlan = $plan->replicate();
            $newPlan->title = $newPlan->title . ' (Copia)';
            $newPlan->assigned_by = Auth::id();
            $newPlan->start_date = now()->format('Y-m-d');
            $newPlan->end_date = now()->addDays(30)->format('Y-m-d');
            $newPlan->status = 'activo';
            $newPlan->created_at = now();
            $newPlan->updated_at = now();
            $newPlan->save();

            // Duplicate plan elements
            foreach ($plan->elements as $element) {
                $newElement = $element->replicate();
                $newElement->plan_id = $newPlan->id;
                $newElement->created_at = now();
                $newElement->updated_at = now();
                $newElement->save();
            }

            DB::commit();

            return redirect()
                ->route("{$this->routeName}edit", $newPlan->id)
                ->with('success', 'Plan duplicado correctamente');
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()
                ->back()
                ->with('error', 'Error al duplicar el plan: ' . $e->getMessage());
        }
    }
}
