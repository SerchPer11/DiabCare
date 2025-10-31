<?php

namespace App\Http\Resources\Patient;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Appointment;
use App\Models\Doctor\Recomendation;
use App\Http\Resources\AppointmentResource;
use App\Http\Resources\Doctor\RecomendationResource;

class ClinicalLogResource extends JsonResource
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
            'event_type' => $this->event_type,
            'event_date' => $this->created_at->format('d/m/Y'),
            
            'event_time' => $this->created_at->format('H:i'),
            
            'event_iso' => $this->created_at->toIso8601String(),

            'doctor' => $this->whenLoaded('doctor', function () {
                return [
                    'id' => $this->doctor->id,
                    'name' => $this->doctor->name,
                    'last_name' => $this->doctor->last_name,
                ];
            }),

            'activity' => $this->whenLoaded('loggable', function () {
                if ($this->loggable instanceof Appointment) {
                    return new AppointmentResource($this->loggable);
                } else if ($this->loggable instanceof Recomendation) {
                    return new RecomendationResource($this->loggable);
                }

                return null;
            }),

            'activity_type' => class_basename($this->loggable_type),
        ];
    }
}
