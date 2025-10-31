<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
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
            'patient' => $this->whenLoaded('patient'),
            'doctor' => $this->whenLoaded('doctor'),
            'date' => $this->date,
            'time' => $this->time,
            'modality' => $this->modality,
            'reason' => $this->reason,
            'additional_notes' => $this->additional_notes,
            'video_call_link' => $this->video_call_link,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
