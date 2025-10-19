<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Resources\Admin\RoleResource;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use App\Traits\Filterable;
use App\Models\Module;
use Inertia\Inertia;

class RoleController extends Controller
{
    use Filterable;

    protected $routeName;
    protected $source;
    protected $model;

    public function __construct()
    {
        $this->routeName = 'roles.';
        $this->source = 'Admin/Role/Pages/';
        $this->model = new Role();

        $this->middleware("permission:{$this->routeName}index")->only(['index', 'show']);
        $this->middleware("permission:{$this->routeName}create")->only(['store', 'create']);
        $this->middleware("permission:{$this->routeName}edit")->only(['edit', 'update']);
        $this->middleware("permission:{$this->routeName}delete")->only(['destroy']);
    }

    public function index(Request $request)
    {
        $filters = $this->getFiltersBase($request->query());
        $query = $this->model->query()->when($filters->search, function ($query, $search) {
            $query->where('name', 'LIKE', '%' . $search . '%')
                ->orWhere('description', 'LIKE', '%' . $search . '%');
        });

        $roles = $query->orderBy($filters->order, $filters->direction)
            ->paginate($filters->rows)
            ->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'title'         => 'Roles',
            'roles'         => RoleResource::collection($roles),
            'routeName'     => $this->routeName,
            'filters'       => $filters
        ]);
    }

    public function create()
    {
        return Inertia::render("{$this->source}Create", [
            'title'         => 'Crear Rol',
            'routeName'     => $this->routeName,
            'modules'       => Module::orderBy('key')->get(['id', 'name', 'description', 'key']),
            'permissions'   => Permission::get(['id', 'name', 'description', 'module_key'])->groupBy('module_key')->toArray(),
        ]);
    }

    public function store(StoreRoleRequest $request)
    {
        $role = Role::create($request->validated());
        if ($request->has('permissions')) {
            $role->syncPermissions($request->input('permissions'));
        }
        return redirect()->route("{$this->routeName}index")
            ->with('success', 'Rol creado exitosamente.');
    }

    public function edit(Role $role)
    {
        $role->load('permissions');
        return Inertia::render("{$this->source}Edit", [
            'title'         => 'Editar Rol',
            'role'          => new RoleResource($role),
            'modules'       => Module::orderBy('key')->get(['id', 'name', 'description', 'key']),
            'permissions'   => Permission::get(['id', 'name', 'description', 'module_key'])->groupBy('module_key')->toArray(),
            'routeName'     => $this->routeName,
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role->update($request->validated());
        if ($request->has('permissions')) {
            $role->syncPermissions($request->input('permissions'));
        }
        return redirect()->route("{$this->routeName}index")
            ->with('success', 'Rol actualizado exitosamente.');
    }

    public function destroy(Role $role)
    {
        Cache::forget("role.{$role->id}");
        $role->delete();
        return redirect()->route("{$this->routeName}index")
            ->with('success', 'Rol eliminado exitosamente.');
    }
}
