<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doctor\Recomendation;
use App\Traits\Filterable;
use Inertia\Inertia;
use App\Http\Resources\Doctor\RecomendationResource;
use App\Models\Admin\Catalogs\RecomendationType;
use App\Models\User;
use App\Http\Requests\Doctor\StoreRecomendationRequest;
use App\Http\Requests\Doctor\UpdateRecomendationRequest;
use Illuminate\Support\Facades\DB;
use App\DTOs\PhotoStorageConfig;
use App\Services\PhotoService;
use App\Services\FileService;
use App\DTOs\FileStorageConfig;
use LaravelLang\Publisher\Console\Update;

class RecomendationController extends Controller
{
    use Filterable;

    protected $routeName;
    protected $source;
    protected $model;
    protected PhotoStorageConfig $configPhotos;
    protected PhotoService $photoService;
    protected FileService $fileService;
    protected FileStorageConfig $configFiles;

    public function __construct(PhotoService $photoService, FileService $fileService)
    {
        $this->routeName = 'doctor.recomendations.';
        $this->source = 'Doctor/Recomendations/Pages/';
        $this->model = new Recomendation();
        $this->configPhotos = new PhotoStorageConfig('public', 'Recomendations/photos', 'photos');
        $this->photoService = $photoService;
        $this->fileService = $fileService;
        $this->configFiles = new FileStorageConfig('private', 'Recomendations/files', 'files');

        $this->middleware("permission:{$this->routeName}index")->only(['index', 'show']);
        $this->middleware("permission:{$this->routeName}create")->only(['store', 'create']);
        $this->middleware("permission:{$this->routeName}edit")->only(['edit', 'update']);
        $this->middleware("permission:{$this->routeName}delete")->only(['destroy']);
    }

    public function index(Request $request)
    {
        $filters = $this->getFiltersBase($request->query());
        $query = $this->model->with(['patient', 'doctor', 'recomendationType'])
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

    public function create()
    {
        $priorities = [
            ['value' => 'low', 'label' => 'Baja'],
            ['value' => 'medium', 'label' => 'Media'],
            ['value' => 'high', 'label' => 'Alta'],
        ];
        $doctors = User::role('doctor')
            ->select(['id', 'name', 'last_name', DB::raw("CONCAT(name, ' ', last_name) as full_name")])
            ->get();
        $patients = User::role('patient')
            ->select(['id', 'name', 'last_name', DB::raw("CONCAT(name, ' ', last_name) as full_name")])
            ->get();
        
        return Inertia::render("{$this->source}Create", [
            'title'           => 'Recomendaciones',
            'routeName'       => $this->routeName,
            'doctors'         => $doctors,
            'patients'        => $patients,
            'types'           => RecomendationType::all(),
            'priorities'      => $priorities,
            'doctors'         => $doctors,
            'patients'        => $patients,
        ]);
    }

    public function store(StoreRecomendationRequest $request)
    {
        DB::Transaction(function () use ($request) {
            $validated = $request->validated();
            $recomendation = Recomendation::create($validated);
            $this->photoService->storePhotos($recomendation, $validated['photos'] ?? [], $this->configPhotos);
            $this->fileService->storeFiles($recomendation,  $validated['files'] ?? [], $this->configFiles);
        });

        return redirect()->route($this->routeName . 'index')
            ->with('success', 'Recomendación creada correctamente.');
    }

    public function edit(Recomendation $recomendation)
    {
        $priorities = [
            ['value' => 'low', 'label' => 'Baja'],
            ['value' => 'medium', 'label' => 'Media'],
            ['value' => 'high', 'label' => 'Alta'],
        ];
        $doctors = User::role('doctor')
            ->select(['id', 'name', 'last_name', DB::raw("CONCAT(name, ' ', last_name) as full_name")])
            ->get();
        $patients = User::role('patient')
            ->select(['id', 'name', 'last_name', DB::raw("CONCAT(name, ' ', last_name) as full_name")])
            ->get();
        
        return Inertia::render("{$this->source}Edit", [
            'title'           => 'Recomendaciones',
            'routeName'       => $this->routeName,
            'doctors'         => $doctors,
            'patients'        => $patients,
            'types'           => RecomendationType::all(),
            'priorities'      => $priorities,
            'doctors'         => $doctors,
            'patients'        => $patients,
            'recomendation'   => new RecomendationResource($recomendation->load(['photos', 'files'])),
        ]);
    }

    public function update(UpdateRecomendationRequest $request, Recomendation $recomendation)
    {
        DB::Transaction(function () use ($request, $recomendation) {
            $validated = $request->validated();
            $recomendation->update($validated);
            $this->photoService->syncPhotos($recomendation, $validated['photos'] ?? [], $this->configPhotos);
            $this->fileService->syncFiles($recomendation,  $validated['files'] ?? [], $this->configFiles);
        });

        return redirect()->route($this->routeName . 'index')
            ->with('success', 'Recomendación actualizada correctamente.');
    }

    public function destroy(Recomendation $recomendation)
    {
        DB::Transaction(function () use ($recomendation) {
            $this->photoService->deletePhotos($recomendation->photos, $this->configPhotos->disk,true);
            $this->fileService->deleteFiles($recomendation->files, $this->configFiles->disk,true);
            $recomendation->delete();
        });

        return redirect()->route($this->routeName . 'index')
            ->with('success', 'Recomendación eliminada correctamente.');
    }
}
