<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\UpdateModuleRequest;
use App\Http\Requests\Admin\StoreModuleRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ModuleResource;
use App\Models\Module;
use App\Traits\Filterable;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class ModuleController extends Controller
{
    use Filterable;
    protected $routeName;
    protected $source;
    protected $model;

    public function __construct()
    {
        $this->routeName = 'modules.';
        $this->source = 'Admin/Module/Pages/';
        $this->model = new Module();

       $this->middleware("permission:{$this->routeName}index")->only(['index', 'show']);
        $this->middleware("permission:{$this->routeName}create")->only(['store', 'create']);
        $this->middleware("permission:{$this->routeName}edit")->only(['edit', 'update']);
        $this->middleware("permission:{$this->routeName}delete")->only(['destroy']);
    }

    public function index(Request $request) : Response
    {
        $filters = $this->getFiltersBase($request->query());
        $query = $this->model->query()->when($filters->search, function ($query, $search) {
            $query->where('name', 'LIKE', '%' . $search . '%')
                ->orWhere('key', 'LIKE', '%' . $search . '%');
        });

        $modules = $query->orderBy($filters->order, $filters->direction)
            ->paginate($filters->rows)
            ->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'title' => 'Módulos',
            'routeName' => $this->routeName,
            'modules' =>  ModuleResource::collection($modules),
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        return Inertia::render("{$this->source}Create", [
            'title' => 'Módulos',
            'routeName' => $this->routeName,
        ]);
    }

    public function store(StoreModuleRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = Auth::id();
        Module::create($validatedData);
        return redirect()->route("{$this->routeName}index")->with('success', 'Módulo creado con éxito!');
    }

    public function edit(Module $module)
    {
        return Inertia::render("{$this->source}Edit", [
            'title' => 'Módulos',
            'routeName' => $this->routeName,
            'module' => $module
        ]);
    }

    public function update(UpdateModuleRequest $request, Module $module)
    {
        $module->update($request->validated());
        if (!$module->user_id) {
            $module->user_id = Auth::id();
            $module->save();
        }
        return redirect()->route("{$this->routeName}index")->with('success', 'Módulo modificado con éxito!');
    }

    public function destroy(Module $module)
    {
        $module->delete();
        return redirect()->route("{$this->routeName}index")->with('success', 'Módulo eliminado con éxito!');
    }
}
