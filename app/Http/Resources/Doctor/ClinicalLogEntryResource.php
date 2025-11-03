<?php

namespace App\Http\Resources\Doctor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\PhotoResource;
use App\Http\Resources\FileResource;
use Carbon\Carbon;

class ClinicalLogEntryResource extends JsonResource
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
            'patient_id' => $this->patient_id,
            'doctor_id' => $this->doctor_id,
            'event_type' => $this->event_type,
            'event_type_name' => $this->event_type_name,
            'title' => $this->title,
            'description' => $this->description,
            'notes' => $this->notes,
            'event_datetime' => $this->event_datetime?->format('Y-m-d H:i:s'),
            'event_datetime_formatted' => $this->event_datetime_formatted,
            'event_datetime_human' => $this->event_datetime_human,
            'related_type' => $this->related_type,
            'related_id' => $this->related_id,
            'is_active' => $this->is_active,
            
            // Relationships
            'patient' => $this->when($this->relationLoaded('patient') && $this->patient, function () {
                return [
                    'id' => $this->patient->id,
                    'name' => $this->patient->name ?? '',
                    'last_name' => $this->patient->last_name ?? '',
                    'full_name' => ($this->patient->name ?? '') . ' ' . ($this->patient->last_name ?? ''),
                    'email' => $this->patient->email ?? '',
                ];
            }) ?: [
                'id' => $this->patient_id,
                'name' => 'Paciente no encontrado',
                'last_name' => '',
                'full_name' => 'Paciente no encontrado',
                'email' => '',
            ],
            
            'doctor' => $this->when($this->relationLoaded('doctor') && $this->doctor, function () {
                return [
                    'id' => $this->doctor->id,
                    'name' => $this->doctor->name ?? '',
                    'last_name' => $this->doctor->last_name ?? '',
                    'full_name' => ($this->doctor->name ?? '') . ' ' . ($this->doctor->last_name ?? ''),
                ];
            }) ?: [
                'id' => $this->doctor_id,
                'name' => 'Doctor no encontrado',
                'last_name' => '',
                'full_name' => 'Doctor no encontrado',
            ],
            
            'related' => $this->whenLoaded('related', function () {
                if (!$this->related) return null;
                
                return [
                    'type' => $this->related_type,
                    'id' => $this->related_id,
                    'name' => $this->getRelatedName(),
                    'data' => $this->related,
                ];
            }),
            
            // Archivos adjuntos
            'photos' => PhotoResource::collection($this->whenLoaded('photos')),
            'files' => FileResource::collection($this->whenLoaded('files')),
            
            // Computed properties
            'has_attachments' => $this->has_attachments,
            'related_name' => $this->getRelatedName(),
            
            // Timestamps
            'created_at' => $this->created_at?->format('d/m/Y H:i'),
            'updated_at' => $this->updated_at?->format('d/m/Y H:i'),
            'created_at_human' => $this->created_at?->diffForHumans(),
            'updated_at_human' => $this->updated_at?->diffForHumans(),
        ];
    }
    
    /**
     * Get related item name based on type
     */
    private function getRelatedName()
    {
        if (!$this->related) return null;

        switch ($this->related_type) {
            case 'App\\Models\\Appointment':
                return "Cita: " . $this->related->patient->name . " - " . 
                       Carbon::parse($this->related->date)->format('d/m/Y');
            case 'App\\Models\\Plan':
                return "Plan: " . $this->related->title;
            case 'App\\Models\\Doctor\\Catalogs\\Medication':
                return "Medicamento: " . $this->related->name;
            default:
                return 'Elemento relacionado';
        }
    }
}
