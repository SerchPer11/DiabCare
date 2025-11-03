<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
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
            'patient_id' => $this->patient_id,
            'plan_type_id' => $this->plan_type_id,
            'title' => $this->title,
            'description' => $this->description,
            'start_date' => $this->start_date ? $this->start_date->format('Y-m-d') : null,
            'end_date' => $this->end_date ? $this->end_date->format('Y-m-d') : null,
            'status' => $this->status,
            
            // Campos de adherencia
            'adherence' => [
                'overall_percentage' => $this->overall_adherence,
                'status' => $this->adherence_status,
                'status_spanish' => $this->adherence_status_spanish,
                'days_tracked' => $this->days_tracked,
                'total_plan_days' => $this->total_plan_days,
                'days_remaining' => $this->total_plan_days - $this->days_tracked,
                'last_tracked_date' => $this->last_tracked_date?->format('d/m/Y'),
                'should_track_today' => $this->shouldBeTrackedToday(),
                'is_currently_active' => $this->isCurrentlyActive(),
                'vigency_status' => $this->vigency_status,
            ],
            'patient' => $this->when($this->patient, [
                'id' => $this->patient->id ?? null,
                'name' => $this->patient->name ?? null,
                'last_name' => $this->patient->last_name ?? null,
                'second_last_name' => $this->patient->second_last_name ?? null,
                'email' => $this->patient->email ?? null,
            ]),
            'plan_type' => $this->when($this->planType, [
                'id' => $this->planType->id ?? null,
                'name' => $this->planType->name ?? null,
                'description' => $this->planType->description ?? null,
            ]),
            'elements' => $this->whenLoaded('elements', function () {
                return $this->elements->map(function ($element) {
                    return [
                        'id' => $element->id,
                        'food_id' => $element->food_id,
                        'exercise_id' => $element->exercise_id,
                        'quantity' => $element->quantity,
                        'unit' => $element->unit,
                        'frequency' => $element->frequency,
                        'intensity' => $element->intensity,
                        'time_schedule' => $element->time_schedule,
                        'instructions' => $element->instructions,
                        'notes' => $element->notes,
                        'order' => $element->order,
                        'food' => $element->food ? [
                            'id' => $element->food->id,
                            'name' => $element->food->name,
                            'calories' => $element->food->calories,
                            'protein' => $element->food->protein,
                            'carbohydrates' => $element->food->carbohydrates,
                            'fat' => $element->food->fat,
                            'fiber' => $element->food->fiber,
                            'food_group' => $element->food->foodGroup ? [
                                'id' => $element->food->foodGroup->id,
                                'name' => $element->food->foodGroup->name,
                            ] : null,
                            'unit' => $element->food->unit ? [
                                'id' => $element->food->unit->id,
                                'name' => $element->food->unit->name,
                                'abbreviation' => $element->food->unit->abbreviation,
                            ] : null,
                        ] : null,
                        'exercise' => $element->exercise ? [
                            'id' => $element->exercise->id,
                            'name' => $element->exercise->name,
                            'description' => $element->exercise->description,
                            'intensity' => $element->exercise->intensity,
                            'duration_minutes' => $element->exercise->duration_minutes,
                            'calories_burned' => $element->exercise->calories_burned,
                            'sets' => $element->exercise->sets,
                            'repetitions' => $element->exercise->repetitions,
                            'rest_seconds' => $element->exercise->rest_seconds,
                            'equipment' => $element->exercise->equipment,
                            'contraindications' => $element->exercise->contraindications,
                            'notes' => $element->exercise->notes,
                            'exercise_type' => $element->exercise->exerciseType ? [
                                'id' => $element->exercise->exerciseType->id,
                                'name' => $element->exercise->exerciseType->name,
                            ] : null,
                        ] : null,
                    ];
                });
            }),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
