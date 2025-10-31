<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurveyRequest;
use App\Http\Requests\UpdateSurveyRequest;
use App\Http\Requests\StoreSurveyResponseRequest;
use App\Http\Resources\SurveyResource;
use App\Http\Resources\SurveyResponseResource;
use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;

use App\Traits\Filterable;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SurveyController extends Controller
{
    use Filterable;

    protected $routeName;
    protected $source;
    protected $model;

    public function __construct()
    {
        $this->routeName = 'surveys.';
        $this->source = 'Survey/Pages/';
        $this->model = new Survey();
        
        // DEPRECATED: Este controlador ya no se usa. 
        // Usar DoctorSurveyController y PatientSurveyController en su lugar
        abort(404, 'Este endpoint ha sido deprecado. Use las rutas específicas por rol.');
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $filters = $this->getFiltersBase($request->query());
        
        $query = $this->model->with(['questions', 'creator']);
        
        // Si es paciente, solo mostrar encuestas disponibles que no haya respondido
        if ($user->hasRole('patient')) {
            $query->available()
                ->whereDoesntHave('responses', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
        }
        // Si es doctor, mostrar todas las encuestas que creó
        elseif ($user->hasRole('doctor')) {
            $query->where('created_by', $user->id);
        }

        $query->when($filters->search, function ($query, $search) {
            $query->where('title', 'LIKE', '%' . $search . '%')
                ->orWhere('description', 'LIKE', '%' . $search . '%')
                ->orWhereHas('questions', function ($q) use ($search) {
                    $q->where('question', 'LIKE', '%' . $search . '%');
                });
        });

        $surveys = $query->orderBy($filters->order, $filters->direction)
            ->paginate($filters->rows)
            ->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'title'     => 'Encuestas',
            'surveys'   => SurveyResource::collection($surveys),
            'routeName' => $this->routeName,
            'filters'   => $filters,
            'userRole'  => $user->roles->first()?->name
        ]);
    }

    public function create()
    {
        return Inertia::render("{$this->source}Create", [
            'title'     => 'Create Survey',
            'routeName' => $this->routeName,
        ]);
    }

    public function store(StoreSurveyRequest $request)
    {
        DB::transaction(function () use ($request) {
            $surveyData = $request->only(['title', 'description', 'instructions', 'starts_at', 'ends_at']);
            $surveyData['created_by'] = $request->user()->id;
            $surveyData['is_active'] = true;
            
            $survey = $this->model->create($surveyData);
            
            foreach ($request->questions as $index => $q) {
                $survey->questions()->create([
                    'question' => $q['question'],
                    'order' => $index + 1,
                    'is_required' => $q['is_required'] ?? true
                ]);
            }
        });
        
        return redirect()->route($this->routeName . 'index')
            ->with('success', 'Encuesta creada exitosamente.');
    }

    public function edit(Survey $survey)
    {
        $survey->load('questions');
        return Inertia::render("{$this->source}Edit", [
            'title'     => 'Edit Survey',
            'routeName' => $this->routeName,
            'survey'    => new SurveyResource($survey),
        ]);
    }

    public function update(UpdateSurveyRequest $request, Survey $survey)
    {
        DB::transaction(function () use ($request, $survey) {
            $survey->update($request->only(['title', 'description']));
            // Optionally update questions here
        });
        return redirect()->route($this->routeName . 'index')->with('success', 'Survey updated successfully.');
    }

    public function destroy(Survey $survey)
    {
        $survey->delete();
        return redirect()->route($this->routeName . 'index')->with('success', 'Survey deleted successfully.');
    }

    public function show(Survey $survey, Request $request)
    {
        $survey->load('questions');
        // Detect if the route is surveys.answer (for answering) or surveys.show (for admin view)
        $routeName = $request->route()->getName();
        if ($routeName === 'surveys.answer') {
            return Inertia::render("{$this->source}Answer", [
                'title'     => 'Answer Survey',
                'routeName' => $this->routeName,
                'survey'    => new SurveyResource($survey),
            ]);
        }
        return Inertia::render("{$this->source}Show", [
            'title'     => 'Survey Details',
            'routeName' => $this->routeName,
            'survey'    => new SurveyResource($survey),
        ]);
    }

    public function submitResponse(StoreSurveyResponseRequest $request, Survey $survey)
    {
        $user = $request->user();
        
        // Verificar que no haya respondido ya
        if ($survey->hasResponseFrom($user->id)) {
            return redirect()->route($this->routeName . 'index')
                ->with('error', 'Ya has respondido esta encuesta anteriormente.');
        }

        DB::transaction(function () use ($request, $survey, $user) {
            $response = SurveyResponse::create([
                'survey_id' => $survey->id,
                'user_id' => $user->id,
            ]);
            
            foreach ($request->answers as $answer) {
                $response->answers()->create([
                    'survey_question_id' => $answer['survey_question_id'],
                    'likert_value' => $answer['likert_value'],
                    'comment' => $answer['comment'] ?? null,
                ]);
            }
            
            // Marcar como completa
            $response->markAsComplete();
        });
        
        return redirect()->route($this->routeName . 'index')
            ->with('success', 'Respuesta enviada exitosamente. ¡Gracias por tu participación!');
    }
}
