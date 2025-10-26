<?php

namespace App\Http\Controllers\Doctor\Catalogs;

use App\Models\Admin\Catalogs\MedicationType;
use App\Models\Admin\Catalogs\MedicationPresentation;
use App\Models\Admin\Catalogs\MedicationAdministration;
use App\Models\Admin\Catalogs\Unit;
use App\Http\Resources\Doctor\Catalogs\MedicationResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\Catalogs\UpdateMedicationRequest;
use App\Http\Requests\Doctor\Catalogs\StoreMedicationRequest;
use App\Models\Doctor\Catalogs\Medication;
use Illuminate\Http\Request;
use App\Traits\Filterable;
use Inertia\Inertia;
use App\Services\PhotoService;
use Illuminate\Support\Facades\DB;
use App\DTOs\PhotoStorageConfig;

class MedicationController extends Controller
{
    use Filterable;

    protected $routeName;
    protected $source;
    protected $model;
    protected PhotoStorageConfig $configPhotos;
    protected PhotoService $photoService;

    public function __construct(PhotoService $photoService)
    {
        $this->routeName = 'doctor.catalogs.medications.';
        $this->source = 'Doctor/Catalogs/Medications/Pages/';
        $this->model = new Medication();
        $this->configPhotos = new PhotoStorageConfig('public', 'medication/photos', 'photos');
        $this->photoService = $photoService;

        $this->middleware("permission:{$this->routeName}index")->only(['index', 'show']);
        $this->middleware("permission:{$this->routeName}create")->only(['store', 'create']);
        $this->middleware("permission:{$this->routeName}edit")->only(['edit', 'update']);
        $this->middleware("permission:{$this->routeName}delete")->only(['destroy']);
    }

    public function index(Request $request)
    {
        $filters = $this->getFiltersBase($request->query());
        $query = $this->model->with('type', 'presentation', 'administration', 'unit')
            ->when($filters->search, function ($query, $search) {
                // Agrupamos todas las condiciones de búsqueda en un solo bloque
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'LIKE', '%' . $search . '%')
                        ->orWhereHas('type', function ($q) use ($search) {
                            $q->where('name', 'LIKE', '%' . $search . '%');
                        })
                        ->orWhereHas('presentation', function ($q) use ($search) {
                            $q->where('name', 'LIKE', '%' . $search . '%');
                        })
                        ->orWhereHas('administration', function ($q) use ($search) {
                            $q->where('name', 'LIKE', '%' . $search . '%');
                        })
                        ->orWhereHas('unit', function ($q) use ($search) {
                            // Buscamos también por el nombre completo de la unidad, no solo la abreviatura
                            $q->where('name', 'LIKE', '%' . $search . '%')
                                ->orWhere('abbreviation', 'LIKE', '%' . $search . '%');
                        });
                });
            });

        $medications = $query->orderBy($filters->order, $filters->direction)
            ->paginate($filters->rows)
            ->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'title'         => 'Medicamentos',
            'medications'   => MedicationResource::collection($medications),
            'routeName'     => $this->routeName,
            'filters'       => $filters
        ]);
    }

    public function create()
    {
        return Inertia::render("{$this->source}Create", [
            'title'         => 'Medicamentos',
            'types'        => MedicationType::all(),
            'presentations' => MedicationPresentation::all(),
            'administrations' => MedicationAdministration::all(),
            'units'         => Unit::where('type', 'med')->orWhere('type', 'stnd')->get(),
            'routeName'     => $this->routeName,
        ]);
    }

    public function store(StoreMedicationRequest $request)
    {
        DB::Transaction(function () use ($request) {

            $medication = Medication::create($request->validated());

            $this->photoService->storePhotos($medication, $request->file('photos') ?? [], $this->configPhotos);
        });
        return redirect()->route("{$this->routeName}index")
            ->with('success', 'Medicamento creado exitosamente.');
    }

    public function edit(Medication $medication)
    {
        $medication->load('type', 'presentation', 'administration', 'unit', 'photos');

        return Inertia::render("{$this->source}Edit", [
            'title'         => 'Medicamentos',
            'types'        => MedicationType::all(),
            'presentations' => MedicationPresentation::all(),
            'administrations' => MedicationAdministration::all(),
            'units'         => Unit::where('type', 'med')->orWhere('type', 'stnd')->get(),
            'medication'    => new MedicationResource($medication),
            'routeName'     => $this->routeName,
        ]);
    }

    public function update(UpdateMedicationRequest $request, Medication $medication)
    {
        DB::Transaction(function () use ($request, $medication) {
            $medication->update($request->validated());

            $this->photoService->syncPhotos($medication, $request->file('photos') ?? [], $this->configPhotos);
        });
        return redirect()->route("{$this->routeName}index")
            ->with('success', 'Medicamento actualizado exitosamente.');
    }

    public function destroy(Medication $medication)
    {
        DB::Transaction(function () use ($medication) {
            $this->photoService->deletePhotos($medication->photos, $this->configPhotos->disk, true);

            $medication->delete();
        });

        return redirect()->route("{$this->routeName}index")
            ->with('success', 'Medicamento eliminado exitosamente.');
    }
}
