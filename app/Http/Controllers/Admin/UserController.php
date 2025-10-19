<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use App\Traits\Filterable;
use App\Models\User;
use Inertia\Inertia;

class UserController extends Controller
{
    use Filterable;

    protected $routeName;
    protected $source;
    protected $model;

    public function __construct()
    {
        $this->routeName = 'users.';
        $this->source = 'Admin/Users/Pages/';
        $this->model = new User();

        $this->middleware("permission:{$this->routeName}index")->only(['index', 'show']);
        $this->middleware("permission:{$this->routeName}create")->only(['store', 'create']);
        $this->middleware("permission:{$this->routeName}edit")->only(['edit', 'update']);
        $this->middleware("permission:{$this->routeName}delete")->only(['destroy']);
    }

    public function index(Request $request)
    {
        $filters = $this->getFiltersBase($request->query());
        $query = $this->model->with('roles')->when($filters->search, function ($query, $search) {
                $query->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('email', 'LIKE', '%' . $search . '%')
                    ->orWhereHas('roles', function ($query) use ($search) {
                        $query->where('name', 'LIKE', '%' . $search . '%');
                    });
            });

        $users = $query->orderBy($filters->order, $filters->direction)
            ->paginate($filters->rows)
            ->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'title'         => 'Usuarios',
            'users'         => UserResource::collection($users),
            'routeName'     => $this->routeName,
            'filters'       => $filters
        ]);
    }

    public function create()
    {
        return Inertia::render("{$this->source}Create", [
            'title'       => 'Crear usuario',
            'routeName'   => $this->routeName,
            'roles'       => Role::all(),
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $user = $this->model::create($request->validated());
        $user->syncRoles($request->roles);
        return redirect()->route("{$this->routeName}index")->with('success', 'Usuario creado con éxito');
    }

    public function edit(User $user)
    {
        $user->load('roles');
        return Inertia::render("{$this->source}Edit", [
            'title'       => 'Editar usuario',
            'routeName'   => $this->routeName,
            'user'        => new UserResource($user),
            'roles'       => Role::all(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        if (!$request->filled('password')) {
            unset($data['password']);
        }

        $user->update($data);
        $user->syncRoles($request->roles);
        return redirect()->route("{$this->routeName}index")->with('success', 'Usuario modificado con éxito');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route("{$this->routeName}index")->with('success', 'Usuario eliminado con éxito');
    }
}
