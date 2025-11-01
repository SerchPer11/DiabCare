<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SurveyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title ?? 'Sin título',
            'description' => $this->description,
            'instructions' => $this->instructions,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'is_active' => $this->is_active ?? false,
            'created_by' => $this->created_by,
            // Always include questions
            'questions' => $this->whenLoaded('questions', function() {
                return SurveyQuestionResource::collection($this->questions);
            }, []),
            'questions_count' => $this->questions_count ?? $this->questions?->count() ?? 0,
            'responses_count' => $this->responses_count ?? 0,
            'completed_responses_count' => $this->completed_responses_count ?? $this->getCompletedResponsesCount(),
            // Calcular porcentaje de respuestas completadas para esta encuesta específica
            'completion_rate' => $this->calculateCompletionRate(),
            // Verificar si la encuesta tiene respuestas (para determinar si es editable)
            'has_responses' => $this->responses_count > 0 || $this->responses()->exists(),
            // Include creator information
            'creator' => $this->whenLoaded('creator', function () {
                return $this->creator ? [
                    'id' => $this->creator->id,
                    'name' => $this->creator->name,
                    'email' => $this->creator->email,
                ] : null;
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
