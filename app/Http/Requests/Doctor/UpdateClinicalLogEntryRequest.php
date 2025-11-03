<?php

namespace App\Http\Requests\Doctor;

use App\Http\Requests\Traits\PhotoRules;
use App\Http\Requests\Traits\FileValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateClinicalLogEntryRequest extends FormRequest
{
    use PhotoRules, FileValidationRules;
    
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Verificar que el usuario está autenticado
        if (!Auth::check()) {
            return false;
        }

        // Obtener la entrada de la ruta
        $entry = $this->route('clinical_logbook');
        
        // Verificar que la entrada existe y que el usuario es el doctor que la creó
        return $entry && $entry->doctor_id === Auth::id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(
            $this->photoRules(10, 5120), // máximo 10 fotos, 5MB cada una
            $this->filesRules(5, 10240), // máximo 5 archivos, 10MB cada uno
            [
                'patient_id' => 'required|exists:users,id',
                'event_type' => 'required|in:observation,medication_adjustment,incident,document',
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:65535',
                'notes' => 'nullable|string|max:65535',
                'related_type' => 'nullable|string|in:App\\Models\\Appointment,App\\Models\\Plan,App\\Models\\Doctor\\Catalogs\\Medication',
                'related_id' => 'nullable|integer|min:1',
                'is_active' => 'sometimes|boolean',
            ]
        );
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'patient_id.required' => 'Debe seleccionar un paciente.',
            'patient_id.exists' => 'El paciente seleccionado no existe.',
            'event_type.required' => 'Debe seleccionar un tipo de evento.',
            'event_type.in' => 'El tipo de evento seleccionado no es válido.',
            'title.required' => 'El título es obligatorio.',
            'title.max' => 'El título no puede exceder 255 caracteres.',
            'description.required' => 'La descripción es obligatoria.',
            'description.max' => 'La descripción es demasiado larga.',
            'notes.max' => 'Las notas son demasiado largas.',
            'related_type.in' => 'El tipo de relación seleccionado no es válido.',
            'related_id.integer' => 'El ID del elemento relacionado debe ser un número.',
            'related_id.min' => 'El ID del elemento relacionado debe ser mayor a 0.',
            'is_active.boolean' => 'El estado debe ser verdadero o falso.',
            
            // Mensajes para archivos
            'photos.array' => 'Las fotos deben ser un arreglo.',
            'photos.max' => 'No puede subir más de 10 fotos.',
            'photos.*.id.integer' => 'El ID de la foto debe ser un número.',
            'photos.*.id.exists' => 'La foto especificada no existe.',
            'photos.*.file.image' => 'Todos los archivos de fotos deben ser imágenes.',
            'photos.*.file.mimes' => 'Las fotos deben ser en formato: jpeg, png, jpg, gif.',
            'photos.*.file.max' => 'Cada foto no puede exceder 5MB.',
            'photos.*.alt_text.string' => 'El texto alternativo debe ser una cadena.',
            'photos.*.alt_text.max' => 'El texto alternativo no puede exceder 255 caracteres.',
            'photos.*.description.string' => 'La descripción debe ser una cadena.',
            'photos.*.description.max' => 'La descripción no puede exceder 500 caracteres.',
        ];
    }

    /**
     * Get custom attribute names
     */
    public function attributes(): array
    {
        return array_merge(
            $this->photoAttributes(),
            $this->filesAttributes(),
            [
                'patient_id' => 'paciente',
                'event_type' => 'tipo de evento',
                'title' => 'título',
                'description' => 'descripción',
                'notes' => 'notas',
                'related_type' => 'tipo de relación',
                'related_id' => 'elemento relacionado',
                'is_active' => 'estado',
            ]
        );
    }

    /**
     * Prepare the data for validation
     */
    protected function prepareForValidation(): void
    {
        // Si se pasa related_type pero no related_id, limpiar related_type
        if ($this->filled('related_type') && !$this->filled('related_id')) {
            $this->merge([
                'related_type' => null,
            ]);
        }
        
        // Si se pasa related_id pero no related_type, limpiar related_id
        if ($this->filled('related_id') && !$this->filled('related_type')) {
            $this->merge([
                'related_id' => null,
            ]);
        }
    }
}
