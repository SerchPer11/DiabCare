<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use App\Traits\Filterable;
use App\Http\Requests\Admin\StorePermissionRequest;
use App\Http\Requests\Admin\UpdatePermissionRequest;
use App\Http\Resources\Admin\PermissionResource;
use App\Models\Module;
use Inertia\Response;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;

class PermissionController extends Controller
{
    use Filterable;
    protected $routeName;
    protected $source;
    protected $model;

    public function __construct()
    {
        $this->routeName = 'permissions.';
        $this->source = 'Admin/Permission/Pages/';
        $this->model = new Permission();

       $this->middleware("permission:{$this->routeName}index")->only(['index', 'show']);
        $this->middleware("permission:{$this->routeName}create")->only(['store', 'create']);
        $this->middleware("permission:{$this->routeName}edit")->only(['edit', 'update']);
        $this->middleware("permission:{$this->routeName}delete")->only(['destroy']);
    }

    public function index(Request $request): Response
    {
        $filters = $this->getFiltersBase($request->query());
        $query = $this->model->query()->when($filters->search, function ($query, $search) {
            $query->where('name', 'LIKE', '%' . $search . '%')
                ->orWhere('description', 'LIKE', '%' . $search . '%')
                ->orWhere('module_key', 'LIKE', '%' . $search . '%');
        });

        $permissions = $query->orderBy($filters->order, $filters->direction)
            ->paginate($filters->rows)
            ->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'title'         => 'Permisos',
            'permissions'   => PermissionResource::collection($permissions),
            'routeName'     => $this->routeName,
            'filters'       => $filters
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render("{$this->source}Create", [
            'title'     => 'Agregar Permisos',
            'routeName' => $this->routeName,
            'modules'   => Module::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePermissionRequest $request): RedirectResponse
    {
        $this->model::create($request->validated());
        return redirect()->route("{$this->routeName}index")->with('success', 'Permiso creado con éxito!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Permission $permission): Response
    {
        return Inertia::render("{$this->source}Edit", [
            'title'         => 'Editar Permisos',
            'routeName'     => $this->routeName,
            'modules'       => Module::orderBy('name')->get(),
            'permission'    => $permission,
        ]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        $permission->update($request->validated());
        Cache::forget('permissions');
        return redirect()->route("{$this->routeName}index")->with('success', 'Permiso modificado con éxito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route("{$this->routeName}index")->with('success', 'Permiso eliminado con éxito');
    }
}
