<?php

namespace App\Http\Resources\Doctor\Catalogs;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Admin\Catalogs\FoodGroupResource;
use App\Http\Resources\Admin\Catalogs\UnitResource;
use App\Http\Resources\PhotoResource;

class FoodResource extends JsonResource
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
            'food_group_id' => $this->food_group_id,
            'calories' => $this->calories,
            'protein' => $this->protein,
            'carbohydrates' => $this->carbohydrates,
            'fats' => $this->fats,
            'fiber' => $this->fiber,
            'description' => $this->description,
            'portion_size' => $this->portion_size,
            'unit_id' => $this->unit_id,
            'is_active' => $this->is_active,
            'foodGroup' => new FoodGroupResource($this->whenLoaded('foodGroup')),
            'unit' => new UnitResource($this->whenLoaded('unit')),
            'photos' => PhotoResource::collection($this->whenLoaded('photos')),
        ];
    }
}
