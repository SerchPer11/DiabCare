<?php

namespace App\Http\Controllers\Doctor\Catalogs;

use App\Models\Doctor\Catalogs\Exercise;
use App\Http\Requests\Doctor\Catalogs\StoreExerciseRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\Catalogs\UpdateExerciseRequest;
use App\Traits\Filterable;
use App\Models\Doctor\Catalogs\ExerciseType;

class ExerciseController extends Controller
{
    use Filterable;

    protected $routeName;
    protected $source;
    protected $model;
    public function __construct()
    {
        $this->routeName = 'doctor.catalogs.exercises.';
        $this->source = 'Doctor/Catalogs/Exercises/Pages/';
        $this->model = new Exercise();

        $this->middleware("permission:{$this->routeName}index")->only(['index', 'show']);
        $this->middleware("permission:{$this->routeName}create")->only(['store', 'create']);
        $this->middleware("permission:{$this->routeName}edit")->only(['edit', 'update']);
        $this->middleware("permission:{$this->routeName}delete")->only(['destroy']);
    }
    // ...existing code...

    public function index(Request $request)
    {
        $filters = $this->getFiltersBase($request->query());
        $query = $this->model->with('exerciseType')
            ->when($filters->search, function ($query, $search) {
                $query->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('description', 'LIKE', '%' . $search . '%')
                    ->orWhereHas('exerciseType', function ($query) use ($search) {
                        $query->where('name', 'LIKE', '%' . $search . '%');
                    });
            });

        $exercises = $query->orderBy($filters->order, $filters->direction)
            ->paginate($filters->rows)
            ->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'title'         => 'Ejercicios',
            'exercises'     => $exercises,
            'routeName'     => $this->routeName,
            'filters'       => $filters
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExerciseRequest $request)
    {
        $exercise = $this->model->create($request->validated());
        return redirect()->route($this->routeName . 'index')
            ->with('success', 'Ejercicio creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function create(Exercise $exercise)
    {
        $exerciseTypes = ExerciseType::all();
        return Inertia::render("{$this->source}Create", [
            'title'         => 'Ejercicios',
            'routeName'     => $this->routeName,
            'exerciseTypes' => [ 'data' => $exerciseTypes ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exercise $exercise)
    {
        $exercise->load('exerciseType');
        $exerciseTypes = ExerciseType::all();
        return Inertia::render("{$this->source}Edit", [
            'title'         => 'Ejercicios',
            'routeName'     => $this->routeName,
            'exercise'      => $exercise,
            'exerciseTypes' => [ 'data' => $exerciseTypes ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExerciseRequest $request, Exercise $exercise)
    {
        $data = $request->validated();
        $exercise->update($data);
        return redirect()->route("{$this->routeName}index")->with('success', 'Ejercicio modificado con éxito');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exercise $exercise)
    {
        $exercise->delete();
        return redirect()->route("{$this->routeName}index")->with('success', 'Ejercicio eliminado con éxito');
    }
}
