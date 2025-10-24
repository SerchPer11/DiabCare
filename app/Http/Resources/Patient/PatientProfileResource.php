<?php

namespace App\Http\Resources\Patient;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientProfileResource extends JsonResource
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
            'weight' => $this->weight,
            'height' => $this->height,
            'blood_type' => $this->blood_type,
            'user' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'last_name' => $this->user?->last_name,
                'email' => $this->user?->email,
            ],
            'pathology' => new PatientPathologiesResource($this->whenLoaded('pathology')),
        ];
    }
}
