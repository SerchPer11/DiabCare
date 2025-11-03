<?php

namespace App\Http\Requests\Doctor\Catalogs;

use Illuminate\Foundation\Http\FormRequest;

class StoreExerciseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'exercise_type_id' => ['required', 'exists:exercise_types,id'],
            'intensity' => ['required', 'string', 'max:100'],
            'duration_minutes' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'calories_burned' => ['required', 'integer', 'min:0'],
            'sets' => ['required', 'integer', 'min:0'],
            'repetitions' => ['required', 'integer', 'min:0'],
            'rest_seconds' => ['required', 'integer', 'min:0'],
            'equipment' => ['nullable', 'string', 'max:255'],
            'contraindications' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del ejercicio es obligatorio.',
            'exercise_type_id.required' => 'El tipo de ejercicio es obligatorio.',
            'exercise_type_id.exists' => 'El tipo de ejercicio seleccionado no es válido.',
            'intensity.required' => 'La intensidad del ejercicio es obligatoria.',
            'duration_minutes.required' => 'La duración en minutos es obligatoria.',
            'calories_burned.required' => 'Las calorías quemadas son obligatorias.',
            'sets.required' => 'El número de series es obligatorio.',
            'repetitions.required' => 'El número de repeticiones es obligatorio.',
            'rest_seconds.required' => 'El tiempo de descanso en segundos es obligatorio.',
        ];
    }
}
