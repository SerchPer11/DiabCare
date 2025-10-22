<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Admin\RoleResource;
use App\Http\Resources\Doctor\DoctorProfileResource;
use App\Models\Doctor\DoctorProfile;

class UserResource extends JsonResource
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
            'last_name' => $this->last_name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'birthdate' => $this->birthdate,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'profile' => $this->whenLoaded('profileable', function () {
                if ($this->profileable instanceof DoctorProfile) {
                    return new DoctorProfileResource($this->profileable);
                } 
                return null;
            }),
        ];
    }
}
