<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSurveyResponseRequest;
use App\Http\Resources\SurveyResource;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Traits\Filterable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PatientSurveyController extends Controller
{
    use Filterable;

    protected $routeName;
    protected $source;
    protected $model;

    public function __construct()
    {
        $this->routeName = 'patient.surveys.';
        $this->source = 'Patient/Survey/Pages/';
        $this->model = new Survey();
        
        // Permisos específicos para pacientes
        $this->middleware("permission:patient.surveys.index")->only(['index']);
        $this->middleware("permission:patient.surveys.submit")->only(['show', 'answer', 'saveProgress', 'submitResponse', 'myResponses', 'showResponse']);
    }

    public function index(Request $request)
    {
        $filters = $this->getFiltersBase($request->query());
        $userId = $request->user()->id;
        
        $query = $this->model->with(['questions'])
            ->available() // Solo encuestas activas y dentro del rango de fechas
            ->whereDoesntHave('responses', function ($query) use ($userId) {
                $query->where('user_id', $userId)->where('is_complete', true); // Solo excluir encuestas completadas
            })
            ->when($filters->search, function ($query, $search) {
                $query->where('title', 'LIKE', '%' . $search . '%')
                    ->orWhere('description', 'LIKE', '%' . $search . '%');
            });

        $surveys = $query->orderBy($filters->order, $filters->direction)
            ->get();

        // Agregar información sobre si ya respondió cada encuesta
        $surveys->transform(function ($survey) use ($userId) {
            $survey->has_responded = $survey->hasResponseFrom($userId);
            return $survey;
        });

        // Obtener respuestas del usuario para agregar progreso (incluyendo en progreso)
        $userResponses = SurveyResponse::where('user_id', $userId)
            ->with('survey', 'answers')
            ->get()
            ->keyBy('survey_id');

        // Crear data de progreso
        $responseProgress = [];
        foreach ($surveys as $survey) {
            $response = $userResponses->get($survey->id);
            if ($response) {
                $totalQuestions = $survey->questions->count();
                $answeredQuestions = $response->answers->count();
                $responseProgress[$survey->id] = [
                    'progress' => $totalQuestions > 0 ? ($answeredQuestions / $totalQuestions) * 100 : 0,
                    'answered' => $answeredQuestions,
                    'total' => $totalQuestions
                ];
            }
        }

        return Inertia::render("{$this->source}Index", [
            'title' => 'Encuestas Disponibles',
            'surveys' => SurveyResource::collection($surveys)->resolve(),
            'myResponses' => $userResponses->values(),
            'responseProgress' => $responseProgress,
            'routeName' => $this->routeName,
            'filters' => $filters
        ]);
    }

    public function show(Survey $survey, Request $request)
    {
        $userId = $request->user()->id;
        
        // Verificar que la encuesta esté disponible para responder
        if (!$survey->is_active) {
            return redirect()->route($this->routeName . 'index')
                ->with('error', 'Esta encuesta no está disponible.');
        }

        // Verificar fechas de vigencia
        if ($survey->starts_at && $survey->starts_at > now()) {
            return redirect()->route($this->routeName . 'index')
                ->with('error', 'Esta encuesta aún no está disponible.');
        }

        if ($survey->ends_at && $survey->ends_at < now()) {
            return redirect()->route($this->routeName . 'index')
                ->with('error', 'Esta encuesta ya no está disponible.');
        }

        // Verificar que no haya respondido completamente ya
        if ($survey->hasResponseFrom($userId)) {
            return redirect()->route($this->routeName . 'index')
                ->with('info', 'Ya has respondido esta encuesta anteriormente.');
        }

        $survey->load('questions');
        
        // Verificar si hay una respuesta en progreso
        $existingResponse = $survey->getInProgressResponseFrom($userId);
        
        return Inertia::render("{$this->source}Answer", [
            'title'     => 'Responder Encuesta: ' . $survey->title,
            'routeName' => $this->routeName,
            'survey'    => (new SurveyResource($survey))->resolve(),
            'existingResponse' => $existingResponse ? $existingResponse->load('answers') : null,
        ]);
    }

    public function saveProgress(Request $request, Survey $survey)
    {
        $user = $request->user();
        
        // Verificaciones de seguridad
        if (!$survey->is_active) {
            return back()->with('error', 'Esta encuesta no está disponible.');
        }

        try {
            DB::transaction(function () use ($request, $survey, $user) {
                // Buscar o crear una respuesta en progreso
                $response = SurveyResponse::firstOrCreate([
                    'survey_id' => $survey->id,
                    'user_id' => $user->id,
                    'is_complete' => false
                ]);
                
                // Eliminar respuestas anteriores para preguntas que se están actualizando
                if ($request->has('answers')) {
                    $questionIds = array_keys($request->answers);
                    $response->answers()->whereHas('question', function($q) use ($questionIds) {
                        $q->whereIn('id', $questionIds);
                    })->delete();
                    
                    // Agregar las nuevas respuestas
                    foreach ($request->answers as $questionId => $answer) {
                        if ($answer !== null) {
                            $question = $survey->questions()->find($questionId);
                            if ($question) {
                                $response->answers()->create([
                                    'survey_question_id' => $question->id,
                                    'likert_value' => $answer,
                                    'comment' => $request->comments[$questionId] ?? null,
                                ]);
                            }
                        }
                    }
                }
                
                // Verificar si ahora todas las preguntas obligatorias han sido respondidas
                $requiredQuestions = $survey->questions()->where('is_required', true)->pluck('id');
                $answeredQuestions = $response->answers()->pluck('survey_question_id');
                
                $allRequiredAnswered = $requiredQuestions->every(function ($questionId) use ($answeredQuestions) {
                    return $answeredQuestions->contains($questionId);
                });
                
                if ($allRequiredAnswered && !$response->is_complete) {
                    // Marcar como completa si todas las preguntas obligatorias están respondidas
                    $response->markAsComplete();
                }
            });
            
            return back()->with('success', 'Progreso guardado correctamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al guardar el progreso');
        }
    }

    public function submitResponse(StoreSurveyResponseRequest $request, Survey $survey)
    {
        $user = $request->user();
        
        // Verificaciones de seguridad
        if (!$survey->is_active) {
            return redirect()->route($this->routeName . 'index')
                ->with('error', 'Esta encuesta no está disponible.');
        }

        if ($survey->hasResponseFrom($user->id)) {
            return redirect()->route($this->routeName . 'index')
                ->with('error', 'Ya has respondido esta encuesta anteriormente.');
        }

        DB::transaction(function () use ($request, $survey, $user) {
            // Buscar respuesta en progreso existente o crear una nueva
            $response = SurveyResponse::firstOrCreate([
                'survey_id' => $survey->id,
                'user_id' => $user->id,
                'is_complete' => false
            ]);
            
            // Eliminar las respuestas anteriores para reemplazarlas con las nuevas
            $response->answers()->delete();
            
            foreach ($request->answers as $answer) {
                $response->answers()->create([
                    'survey_question_id' => $answer['survey_question_id'],
                    'likert_value' => $answer['likert_value'],
                    'comment' => $answer['comment'] ?? null,
                ]);
            }
            
            // Verificar si todas las preguntas obligatorias han sido respondidas
            $requiredQuestions = $survey->questions()->where('is_required', true)->pluck('id');
            $answeredQuestions = $response->answers()->pluck('survey_question_id');
            
            $allRequiredAnswered = $requiredQuestions->every(function ($questionId) use ($answeredQuestions) {
                return $answeredQuestions->contains($questionId);
            });
            
            if ($allRequiredAnswered) {
                // Marcar como completa solo si todas las preguntas obligatorias están respondidas
                $response->markAsComplete();
            }
        });
        
        return redirect()->route($this->routeName . 'index')
            ->with('success', 'Respuesta enviada exitosamente. ¡Gracias por tu participación!');
    }

    public function myResponses(Request $request)
    {
        $filters = $this->getFiltersBase($request->query());
        $userId = $request->user()->id;
        
        $query = SurveyResponse::with(['survey.questions', 'answers.question'])
            ->where('user_id', $userId)
            // Mostrar todas las respuestas (completas e incompletas)
            // El frontend decidirá cómo mostrarlas basado en is_complete y preguntas obligatorias
            ->when($filters->search, function ($query, $search) {
                $query->whereHas('survey', function ($q) use ($search) {
                    $q->where('title', 'LIKE', '%' . $search . '%');
                });
            });

        $responses = $query->orderBy($filters->order, $filters->direction)
            ->paginate($filters->rows)
            ->withQueryString();

        // Generate temporal data for progress tracking
        $temporalData = $this->generateTemporalData($userId);

        return Inertia::render("{$this->source}MyResponses", [
            'title'     => 'Mis Respuestas',
            'responses' => $responses,
            'temporalData' => $temporalData,
            'routeName' => $this->routeName,
            'filters'   => $filters
        ]);
    }

    /* COMENTADO: Método no necesario, ahora se usa modal en el frontend
    public function showResponse(SurveyResponse $response, Request $request)
    {
        // Verificar que la respuesta pertenece al usuario actual
        if ($response->user_id !== $request->user()->id) {
            abort(403, 'No tienes permiso para ver esta respuesta.');
        }

        $response->load(['survey.questions', 'answers.question']);

        return Inertia::render("{$this->source}ShowResponse", [
            'title' => 'Detalle de Respuesta: ' . $response->survey->title,
            'response' => $response,
            'routeName' => $this->routeName,
        ]);
    }
    */

    private function generateTemporalData($userId)
    {
        try {
            // Get responses grouped by month for the last 6 months
            $temporalData = [];
            $startDate = now()->subMonths(5)->startOfMonth();
            
            for ($i = 0; $i < 6; $i++) {
                $monthStart = $startDate->copy()->addMonths($i);
                $monthEnd = $monthStart->copy()->endOfMonth();
                
                $monthlyResponses = SurveyResponse::with(['answers'])
                    ->where('user_id', $userId)
                    ->where('is_complete', true)
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->get();

                $responseCount = $monthlyResponses->count();
                $avgScore = 0;

                if ($responseCount > 0) {
                    $totalScore = 0;
                    $totalAnswers = 0;
                    
                    foreach ($monthlyResponses as $response) {
                        foreach ($response->answers as $answer) {
                            $totalScore += $answer->likert_value ?? 0;
                            $totalAnswers++;
                        }
                    }
                    
                    $avgScore = $totalAnswers > 0 ? $totalScore / $totalAnswers : 0;
                }

                $temporalData[] = [
                    'period' => $monthStart->format('M Y'),
                    'responses' => $responseCount,
                    'avgScore' => round($avgScore, 2),
                    'month' => $monthStart->format('Y-m')
                ];
            }

            return $temporalData;
        } catch (\Exception $e) {
            // Return empty array if there's any issue
            return [];
        }
    }
}
