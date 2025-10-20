<?php

namespace App\Http\Resources\Doctor\Catalogs;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExerciseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'exercise_type' => new ExerciseTypeResource($this->whenLoaded('exerciseType')),
            'intensity' => $this->intensity,
            'duration_minutes' => $this->duration_minutes,
            'description' => $this->description,
            'calories_burned' => $this->calories_burned,
            'sets' => $this->sets,
            'repetitions' => $this->repetitions,
            'rest_seconds' => $this->rest_seconds,
            'equipment' => $this->equipment,
            'contraindications' => $this->contraindications,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
