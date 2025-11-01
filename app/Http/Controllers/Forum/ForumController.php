<?php

namespace App\Http\Controllers\Forum;

use App\Models\Forum\Forum;
use App\Http\Requests\Forum\StoreForumRequest;
use App\Http\Requests\Forum\UpdateForumRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\Forum\ForumResource;
use App\Traits\Filterable;
use Illuminate\Http\Request;
use App\Models\Forum\ForumCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ForumController extends Controller
{
    use Filterable;

    protected $routeName;
    protected $source;
    protected $model;

    public function __construct()
    {
        $this->routeName = 'forum.';
        $this->source = 'Forum/Pages/';
        $this->model = new Forum();
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $this->getFiltersBase($request->query());
        $query = $this->model->with('user', 'status', 'answers', 'answers.user', 'category')
        ->withCount('answers')
            ->when($filters->search, function ($query, $search) {
                $query->where('title', 'LIKE', '%' . $search . '%')
                    ->orWhere('content', 'LIKE', '%' . $search . '%')
                    ->orWhereHas('category', function ($q) use ($search) {
                        $q->where('name', 'LIKE', '%' . $search . '%');
                    });
            });

        $forums = $query->orderBy($filters->order, $filters->direction)
            ->paginate($filters->rows)
            ->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'title'         => 'Foro',
            'questions'  => ForumResource::collection($forums),
            'routeName'     => $this->routeName,
            'filters'       => $filters,
            'categories'    => ForumCategory::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreForumRequest $request)
    {
        DB::Transaction(function () use ($request) {
            $forum = $this->model;
            $forum->title = $request->title;
            $forum->content = $request->content;
            $forum->category_id = $request->category_id;
            $forum->user_id = Auth::user() ? Auth::user()->id : null;
            $forum->forum_status_id = 1;
            $forum->save();
        });

        return redirect()->back()
            ->with('success', 'Discusión/pregunta creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Forum $forum)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Forum $forum)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateForumRequest $request, Forum $forum)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Forum $forum)
    {
        //
    }
}
