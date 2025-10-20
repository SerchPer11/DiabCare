<?php

namespace App\Http\Resources\Doctor\Catalogs;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Admin\Catalogs\MedicationTypeResource;
use App\Http\Resources\Admin\Catalogs\MedicationPresentationResource;
use App\Http\Resources\Admin\Catalogs\MedicationAdministrationResource;
use App\Http\Resources\Admin\Catalogs\UnitResource;
use App\Http\Resources\PhotoResource;

class MedicationResource extends JsonResource
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
            'concentration' => $this->concentration,
            'indications' => $this->indications,
            'contraindications' => $this->contraindications,
            'medication_type_id' => $this->medication_type_id,
            'medication_presentation_id' => $this->medication_presentation_id,
            'medication_administration_id' => $this->medication_administration_id,
            'unit_id' => $this->unit_id,
            'type' => new MedicationTypeResource($this->whenLoaded('type')),
            'presentation' => new MedicationPresentationResource($this->whenLoaded('presentation')),
            'administration' => new MedicationAdministrationResource($this->whenLoaded('administration')),
            'unit' => new UnitResource($this->whenLoaded('unit')),
            'photos' => PhotoResource::collection($this->whenLoaded('photos')),
            'is_active' => $this->is_active,
        ];
    }
}
