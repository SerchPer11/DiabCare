<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Traits\Filterable;
use App\Models\User;
use Inertia\Inertia;

class PatientsController extends Controller
{
    use Filterable;
    protected $routeName;
    protected $source;
    protected $model;

    public function __construct()
    {
        $this->routeName = 'patients.';
        $this->source = 'Doctor/Patients/Pages/';
        $this->model = new User();

        $this->middleware("permission:{$this->routeName}index")->only(['index', 'show']);
    }

    public function index(Request $request)
    {
        $filters = $this->getFiltersBase($request->query());
        $query = $this->model
            ->role('patient')
            ->with('roles')
            ->when($filters->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%')
                        ->orWhere('email', 'LIKE', '%' . $search . '%');
                });
            });

        $patients = $query->orderBy($filters->order, $filters->direction)
            ->paginate($filters->rows)
            ->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'title'         => 'Pacientes',
            'patients'         => UserResource::collection($patients),
            'routeName'     => $this->routeName,
            'filters'       => $filters
        ]);
    }
}
