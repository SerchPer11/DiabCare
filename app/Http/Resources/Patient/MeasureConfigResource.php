<?php

namespace App\Http\Resources\Patient;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Patient\PatientResource;
use App\Http\Resources\Patient\MeasureTypeResource;

class MeasureConfigResource extends JsonResource
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
            'min_value' => $this->min_value,
            'max_value' => $this->max_value,
            'range' => $this->range,
            'severity' => $this->severity,
            'frequency' => $this->frequency,
            'patient_id' => $this->patient_id,
            'measure_type_id' => $this->measure_type_id,
            'is_active' => $this->is_active,
            'patient' => $this->whenLoaded('patient'),
            'measure_type' => new MeasureTypeResource($this->whenLoaded('measureType')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
