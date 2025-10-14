<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Module;
use Inertia\Inertia;


class ModuleController extends Controller
{
    protected $routeName;
    protected $source;

    public function __construct()
    {
        $this->routeName = 'modules.';
        $this->source = 'Admin/Module/Pages/';

       /* $this->middleware("permission:{$this->routeName}index")->only(['index', 'show']);
        $this->middleware("permission:{$this->routeName}store")->only(['store', 'create']);
        $this->middleware("permission:{$this->routeName}update")->only(['edit', 'update']);
        $this->middleware("permission:{$this->routeName}delete")->only(['destroy']);*/
    }

    public function index()
    {
        return Inertia::render("{$this->source}Index", [
            'title' => 'Módulos',
            'routeName' => $this->routeName,
            'modules' => Module::all()
        ]);
    }

    public function create()
    {
        return Inertia::render("{$this->source}Create", [
            'title' => 'Crear Módulo',
            'routeName' => $this->routeName,
        ]);
    }

    public function store(Request $request)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
