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

    // User management
    protected $routeName;
    protected $source;
    protected $model;

    public function __construct()
    {
        // Initialize properties
        $this->routeName = 'users.';
        $this->source = 'Admin/Users/Pages/';
        $this->model = new User();

        // Set middleware for specific routes
        $this->middleware("permission:{$this->routeName}index")->only(['index', 'show']);
        $this->middleware("permission:{$this->routeName}create")->only(['store', 'create']);
        $this->middleware("permission:{$this->routeName}edit")->only(['edit', 'update']);
        $this->middleware("permission:{$this->routeName}delete")->only(['destroy']);
    }

    public function index(Request $request)
    {
        // Get filters and query users
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
        // Render create page
        return Inertia::render("{$this->source}Create", [
            'title'       => 'Usuaios',
            'routeName'   => $this->routeName,
            'roles'       => Role::all(),
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        // Create user and sync roles
        $user = $this->model::create($request->validated());
        $user->syncRoles($request->roles);
        return redirect()->route("{$this->routeName}index")->with('success', 'Usuario creado con éxito');
    }

    public function edit(User $user)
    {
        // Render edit page
        $user->load('roles');
        return Inertia::render("{$this->source}Edit", [
            'title'       => 'Usuarios',
            'routeName'   => $this->routeName,
            'user'        => new UserResource($user),
            'roles'       => Role::all(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        // Update user and sync roles
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
        // Delete user
        $user->delete();
        return redirect()->route("{$this->routeName}index")->with('success', 'Usuario eliminado con éxito');
    }
}
