<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doctor\Recomendation;
use App\Traits\Filterable;
use Inertia\Inertia;
use App\Http\Resources\Doctor\RecomendationResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\Catalogs\RecomendationType;

class PatientRecomendationController extends Controller
{
    use Filterable;

    protected $routeName;
    protected $source;
    protected $model;

    public function __construct()
    {
        $this->routeName = 'patient.recommendations.';
        $this->source = 'Patient/Recomendation/Pages/';
        $this->model = new Recomendation();

        /*$this->middleware("permission:{$this->routeName}index")->only(['index', 'show']);
        $this->middleware("permission:{$this->routeName}create")->only(['store', 'create']);
        $this->middleware("permission:{$this->routeName}edit")->only(['edit', 'update']);
        $this->middleware("permission:{$this->routeName}delete")->only(['destroy']);*/
    }

    public function index(Request $request)
    {
        $filters = $this->getFiltersBase($request->query());
        $query = $this->model->with(['patient', 'doctor', 'recomendationType'])
            ->where('patient_id', Auth::id())
            ->when($filters->search, function ($query, $search) {
                $query->where('title', 'LIKE', '%' . $search . '%')
                    ->orWhere('content', 'LIKE', '%' . $search . '%')
                    ->orWhereHas('patient', function ($q) use ($search) {
                        $q->where('name', 'LIKE', '%' . $search . '%');
                    })
                    ->orWhereHas('doctor', function ($q) use ($search) {
                        $q->where('name', 'LIKE', '%' . $search . '%');
                    })
                    ->orWhereHas('recomendationType', function ($q) use ($search) {
                        $q->where('name', 'LIKE', '%' . $search . '%');
                    });
            });

        $recomendations = $query->orderBy($filters->order, $filters->direction)
            ->paginate($filters->rows)
            ->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'title'         => 'Recomendaciones',
            'recomendations'  => RecomendationResource::collection($recomendations),
            'routeName'     => $this->routeName,
            'filters'       => $filters
        ]);
    }

    public function show(Request $request, $recomendation)
    {
        if ($request->has('notif_id')) {
            $notification = $request->user()
                ->notifications()
                ->where('id', $request->query('notif_id'))
                ->first();

            // 3. Si existe y no está leída, la marcamos
            if ($notification && $notification->unread()) {
                $notification->markAsRead();
            }
        }
        $recomendationFound = $this->model
            ->where('patient_id', Auth::id())
            ->where('id', $recomendation)
            ->firstOrFail();

        if ($recomendationFound->patient_id !== Auth::id()) {
            return redirect()->route($this->routeName . 'index')
                ->with('error', 'No tienes permiso para ver esta recomendación.');
        }

        $priorities = [
            ['value' => 'low', 'label' => 'Baja'],
            ['value' => 'medium', 'label' => 'Media'],
            ['value' => 'high', 'label' => 'Alta'],
        ];
        $doctors = User::role('doctor')
            ->select(['id', DB::raw("CONCAT(name, ' ', last_name, ' ', second_last_name) as name")])
            ->get();
        $patients = User::role('patient')
            ->select(['id', DB::raw("CONCAT(name, ' ', last_name, ' ', second_last_name) as name")])
            ->get();

        return Inertia::render("{$this->source}Show", [
            'title'           => 'Recomendaciones',
            'routeName'       => $this->routeName,
            'doctors'         => $doctors,
            'patients'        => $patients,
            'types'           => RecomendationType::all(),
            'priorities'      => $priorities,
            'doctors'         => $doctors,
            'patients'        => $patients,
            'recomendation'   => new RecomendationResource($recomendationFound->load(['photos', 'files'])),
        ]);
    }
}
