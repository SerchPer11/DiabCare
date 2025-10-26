<?php

namespace App\Http\Controllers\Doctor\Catalogs;

use App\Http\Requests\Doctor\Catalogs\UpdateFoodRequest;
use App\Http\Requests\Doctor\Catalogs\StoreFoodRequest;
use App\Http\Resources\Doctor\Catalogs\FoodResource;
use App\Models\Admin\Catalogs\FoodGroup;
use App\Http\Controllers\Controller;
use App\Models\Doctor\Catalogs\Food;
use App\Models\Admin\Catalogs\Unit;
use Illuminate\Support\Facades\DB;
use App\DTOs\PhotoStorageConfig;
use App\Services\PhotoService;
use Illuminate\Http\Request;
use App\Traits\Filterable;
use Inertia\Inertia;



class FoodController extends Controller
{
    use Filterable;

    protected $routeName;
    protected $source;
    protected $model;
    protected PhotoStorageConfig $configPhotos;
    protected PhotoService $photoService;

    public function __construct(PhotoService $photoService)
    {
        $this->routeName = 'doctor.catalogs.foods.';
        $this->source = 'Doctor/Catalogs/Foods/Pages/';
        $this->model = new Food();
        $this->configPhotos = new PhotoStorageConfig('public', 'food/photos', 'photos');
        $this->photoService = $photoService;

        $this->middleware("permission:{$this->routeName}index")->only(['index', 'show']);
        $this->middleware("permission:{$this->routeName}create")->only(['store', 'create']);
        $this->middleware("permission:{$this->routeName}edit")->only(['edit', 'update']);
        $this->middleware("permission:{$this->routeName}delete")->only(['destroy']);
    }

    public function index(Request $request)
    {
        $filters = $this->getFiltersBase($request->query());
        $query = $this->model->with('foodGroup', 'unit')
            ->when($filters->search, function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'LIKE', '%' . $search . '%')
                        ->orWhereHas('foodGroup', function ($q) use ($search) {
                            $q->where('name', 'LIKE', '%' . $search . '%');
                        })
                        ->orWhereHas('presentation', function ($q) use ($search) {
                            $q->where('name', 'LIKE', '%' . $search . '%');
                        })
                        ->orWhereHas('unit', function ($q) use ($search) {
                            $q->where('name', 'LIKE', '%' . $search . '%')
                                ->orWhere('abbreviation', 'LIKE', '%' . $search . '%');
                        });
                });
            });

        $foods = $query->orderBy($filters->order, $filters->direction)
            ->paginate($filters->rows)
            ->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'title'         => 'Alimentos',
            'foods'   => FoodResource::collection($foods),
            'routeName'     => $this->routeName,
            'filters'       => $filters
        ]);
    }

    public function create()
    {
        return Inertia::render("{$this->source}Create", [
            'title'         => 'Alimentos',
            'foodGroups'    => FoodGroup::all(),
            'units'         => Unit::where('type', 'food')->orWhere('type', 'stnd')->get(),
            'routeName'     => $this->routeName,
        ]);
    }

    public function store(StoreFoodRequest $request)
    {
        DB::Transaction(function () use ($request) {
            $food = Food::create($request->validated());

            $this->photoService->storePhotos($food, $request->file('photos') ?? [], $this->configPhotos);
        });

        return redirect()->route("{$this->routeName}index")
            ->with('success', 'El alimento se ha creado correctamente.');
    }

    public function edit(Food $food)
    {
        return Inertia::render("{$this->source}Edit", [
            'title'         => 'Alimentos',
            'foodGroups'    => FoodGroup::all(),
            'units'         => Unit::where('type', 'food')->orWhere('type', 'stnd')->get(),
            'routeName'     => $this->routeName,
            'food'          => new FoodResource($food),
        ]);
    }

    public function update(UpdateFoodRequest $request, Food $food)
    {
        DB::Transaction(function () use ($request, $food) {
            $food->update($request->validated());

            $this->photoService->syncPhotos($food, $request->file('photos') ?? [], $this->configPhotos);
        });
        return redirect()->route("{$this->routeName}index")
            ->with('success', 'Alimento actualizado exitosamente.');
    }

    public function destroy(Food $food)
    {
        DB::Transaction(function () use ($food) {
            $this->photoService->deletePhotos($food->photos, $this->configPhotos->disk, true);

            $food->delete();
        });

        return redirect()->route("{$this->routeName}index")
            ->with('success', 'Alimento eliminado exitosamente.');
    }
}
