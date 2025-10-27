<?php

namespace App\Http\Resources\Patient;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Patient\MeasureConfigResource;

class MeasureResource extends JsonResource
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
            'value' => $this->value,
            'measured_at' => $this->measured_at,
            'hour_measured' => $this->hour_measured,
            'notes' => $this->notes,
            'measure_config_id' => $this->measure_config_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'measureConfig' => new MeasureConfigResource($this->whenLoaded('measureConfig')),
        ];
    }
}
