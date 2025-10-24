<?php

namespace App\Http\Resources\Patient;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientPathologiesResource extends JsonResource
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
            'diabetes' => $this->diabetes,
            'diabetes_type' => $this->diabetes_type,
            'diabetes_diagnosis_date' => $this->diabetes_diagnosis_date,
            'hypertension' => $this->hypertension,
            'hypertension_type' => $this->hypertension_type,
            'hypertension_diagnosis_date' => $this->hypertension_diagnosis_date,
            'obesity' => $this->obesity,
            'obesity_type' => $this->obesity_type,
            'allergies' => $this->allergies,
            'allergies_details' => $this->allergies_details,
        ];
    }
}
