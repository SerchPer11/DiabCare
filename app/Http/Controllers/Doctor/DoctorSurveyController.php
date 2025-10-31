<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSurveyRequest;
use App\Http\Requests\UpdateSurveyRequest;
use App\Http\Resources\SurveyResource;
use App\Http\Resources\SurveyResponseResource;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Traits\Filterable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DoctorSurveyController extends Controller
{
    use Filterable;

    protected $routeName;
    protected $source;
    protected $model;

    public function __construct()
    {
        $this->routeName = 'doctor.surveys.';
        $this->source = 'Doctor/Survey/Pages/';
        $this->model = new Survey();
        
        // Permisos específicos para doctores
        $this->middleware("permission:doctor.surveys.index")->only(['index', 'show']);
        $this->middleware("permission:doctor.surveys.create")->only(['store', 'create']);
        $this->middleware("permission:doctor.surveys.edit")->only(['edit', 'update']);
        $this->middleware("permission:doctor.surveys.delete")->only(['destroy']);
        $this->middleware("permission:doctor.surveys.results")->only(['results', 'showResults']);
    }

    public function index(Request $request)
    {
        $filters = $this->getFiltersBase($request->query());
        $query = $this->model->with(['questions', 'creator'])
            ->withCount(['questions', 'responses'])
            ->where('created_by', $request->user()->id) // Solo encuestas creadas por el doctor actual
            ->when($filters->search, function ($query, $search) {
                $query->where('title', 'LIKE', '%' . $search . '%')
                    ->orWhere('description', 'LIKE', '%' . $search . '%');
            });

        $surveys = $query->orderBy($filters->order, $filters->direction)
            ->paginate($filters->rows)
            ->withQueryString();

        // Estadísticas generales del doctor
        $doctorSurveysCount = Survey::where('created_by', $request->user()->id)->count();
        $activeSurveysCount = Survey::where('created_by', $request->user()->id)->where('is_active', true)->count();
        $inactiveSurveysCount = $doctorSurveysCount - $activeSurveysCount;
        
        $doctorSurveys = Survey::where('created_by', $request->user()->id)->pluck('id');
        $totalResponses = SurveyResponse::whereIn('survey_id', $doctorSurveys)->count();
        $completedResponses = SurveyResponse::whereIn('survey_id', $doctorSurveys)
            ->where('is_complete', true)
            ->count();

        return Inertia::render("{$this->source}Index", [
            'title'     => 'Gestión de Encuestas',
            'surveys'   => SurveyResource::collection($surveys)->resolve(),
            'stats'     => [
                'total' => $doctorSurveysCount,
                'active' => $activeSurveysCount,
                'inactive' => $inactiveSurveysCount,
                'total_responses' => $totalResponses,
                'average_response_rate' => $totalResponses > 0 ? round(($completedResponses / $totalResponses) * 100) : 0,
            ],
            'routeName' => $this->routeName,
            'filters'   => $filters
        ]);
    }

    public function create()
    {
        return Inertia::render("{$this->source}Create", [
            'title'     => 'Crear Nueva Encuesta',
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

    public function show(Survey $survey)
    {
        // Verificar que el doctor sea el creador de la encuesta
        if ($survey->created_by !== Auth::id()) {
            abort(403, 'No tienes permiso para ver esta encuesta.');
        }

        // Cargar todas las relaciones necesarias
        $survey->load(['questions', 'responses.user', 'creator'])
               ->loadCount(['questions', 'responses']);
        
        return Inertia::render("{$this->source}Show", [
            'title'     => 'Detalles de la Encuesta',
            'routeName' => $this->routeName,
            'survey'    => (new SurveyResource($survey))->resolve(),
        ]);
    }

    public function edit(Survey $survey)
    {
        // Verificar que el doctor sea el creador de la encuesta
        if ($survey->created_by !== Auth::id()) {
            abort(403, 'No tienes permiso para editar esta encuesta.');
        }

        $survey->load(['questions', 'creator']);
        return Inertia::render("{$this->source}Edit", [
            'title'     => 'Editar Encuesta',
            'routeName' => $this->routeName,
            'survey'    => (new SurveyResource($survey))->resolve(),
        ]);
    }

    public function update(UpdateSurveyRequest $request, Survey $survey)
    {
        // Verificar que el doctor sean el creador de la encuesta
        if ($survey->created_by !== Auth::id()) {
            abort(403, 'No tienes permiso para editar esta encuesta.');
        }

        DB::transaction(function () use ($request, $survey) {
            $survey->update($request->only(['title', 'description', 'instructions', 'starts_at', 'ends_at']));
            
            // Actualizar preguntas si se envían
            if ($request->has('questions')) {
                $survey->questions()->delete();
                foreach ($request->questions as $index => $q) {
                    $survey->questions()->create([
                        'question' => $q['question'],
                        'order' => $index + 1,
                        'is_required' => $q['is_required'] ?? true
                    ]);
                }
            }
        });
        
        return redirect()->route($this->routeName . 'index')
            ->with('success', 'Encuesta actualizada exitosamente.');
    }

    public function destroy(Survey $survey)
    {
        // Verificar que el doctor sea el creador de la encuesta
        if ($survey->created_by !== Auth::id()) {
            abort(403, 'No tienes permiso para eliminar esta encuesta.');
        }

        $survey->delete();
        return redirect()->route($this->routeName . 'index')
            ->with('success', 'Encuesta eliminada exitosamente.');
    }

    public function toggleStatus(Survey $survey)
    {
        // Verificar que el doctor sea el creador de la encuesta
        if ($survey->created_by !== Auth::id()) {
            abort(403, 'No tienes permiso para modificar esta encuesta.');
        }

        $survey->update([
            'is_active' => !$survey->is_active
        ]);

        $message = $survey->is_active 
            ? 'Encuesta activada exitosamente.' 
            : 'Encuesta desactivada exitosamente.';

        return redirect()->back()->with('success', $message);
    }

    public function results(Request $request)
    {
        $filters = $this->getFiltersBase($request->query());
        $query = $this->model->with(['questions', 'creator', 'responses.user', 'responses.answers'])
            ->where('created_by', $request->user()->id)
            ->whereHas('responses') // Solo encuestas con respuestas
            ->when($filters->search, function ($query, $search) {
                $query->where('title', 'LIKE', '%' . $search . '%');
            });

        $surveys = $query->orderBy($filters->order, $filters->direction)
            ->paginate($filters->rows)
            ->withQueryString();

        // Estadísticas generales del doctor
        $doctorSurveysCount = Survey::where('created_by', $request->user()->id)->count();
        $doctorSurveys = Survey::where('created_by', $request->user()->id)->pluck('id');
        
        $totalResponses = SurveyResponse::whereIn('survey_id', $doctorSurveys)->count();
        $completedResponses = SurveyResponse::whereIn('survey_id', $doctorSurveys)
            ->where('is_complete', true)
            ->count();
        $pendingResponses = $totalResponses - $completedResponses;

        return Inertia::render("{$this->source}Index", [
            'title'     => 'Resultados de Encuestas',
            'surveys'   => SurveyResource::collection($surveys)->resolve(),
            'stats'     => [
                'total' => $doctorSurveysCount,
                'total_responses' => $totalResponses,
                'completed' => $completedResponses,
                'pending' => $pendingResponses,
                'average_response_rate' => $totalResponses > 0 ? round(($completedResponses / $totalResponses) * 100) : 0,
            ],
            'routeName' => $this->routeName,
            'filters'   => $filters,
            'showingResults' => true // Flag para indicar que estamos mostrando resultados
        ]);
    }

    public function showResults(Survey $survey)
    {
        // Verificar que el doctor sea el creador de la encuesta
        if ($survey->created_by !== Auth::id()) {
            abort(403, 'No tienes permiso para ver los resultados de esta encuesta.');
        }

        // Cargar respuestas completadas
        $survey->load([
            'questions',
            'creator',
            'responses' => function ($query) {
                $query->where('is_complete', true)->with(['user', 'answers']);
            }
        ]);

        // Obtener estadísticas básicas
        $completedResponses = $survey->responses;
        $totalResponses = $completedResponses->count();

        // Calcular estadísticas por pregunta
        $questionResults = [];
        foreach ($survey->questions as $question) {
            $answers = $completedResponses->flatMap->answers->where('survey_question_id', $question->id);
            $questionResults[] = [
                'id' => $question->id,
                'question' => $question->question,
                'totalAnswers' => $answers->count(),
                'average' => $answers->count() > 0 ? round($answers->avg('likert_value'), 2) : 0,
                'distribution' => $answers->groupBy('likert_value')->map->count()->toArray(),
            ];
        }

        // Datos temporales (ejemplo simple)
        $temporalData = [];
        
        return Inertia::render("{$this->source}Results", [
            'title' => 'Resultados: ' . $survey->title,
            'routeName' => $this->routeName,
            'survey' => (new SurveyResource($survey))->resolve(),
            'responses' => $completedResponses,
            'questionResults' => $questionResults,
            'temporalData' => $temporalData,
        ]);
    }
}
