<?php

namespace App\Http\Resources\Doctor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PhotoResource;
use App\Http\Resources\FileResource;
use App\Http\Resources\Admin\Catalogs\RecomendationTypeResource;

class RecomendationResource extends JsonResource
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
            'title' => $this->title,
            'recomendation_type_id' => $this->recomendation_type_id,
            'priority' => $this->priority,
            'content' => $this->content,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'patient' => $this->whenLoaded('patient'),
            'doctor' => $this->whenLoaded('doctor'),
            'patient_id' => $this->patient_id,
            'doctor_id' => $this->doctor_id,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'type' => new RecomendationTypeResource($this->whenLoaded('recomendationType')),
            'photos' => PhotoResource::collection($this->whenLoaded('photos')),
            'files' => FileResource::collection($this->whenLoaded('files')),
        ];
    }
}
