<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Http\Requests\Traits\PhotoRules;
use App\Http\Requests\Traits\FileValidationRules;

class UpdateRecomendationRequest extends FormRequest
{
    use PhotoRules, FileValidationRules;
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
        return array_merge(
            $this->photoRules(),
            $this->filesRules(),
            [
                'title' => [
                    'required',
                    'string',
                    'max:100',
                    Rule::unique('recomendations')->ignore($this->route('recomendation')),
                ],
                'recomendation_type_id' => 'required|exists:recomendation_types,id',
                'priority' => 'required|in:low,medium,high',
                'content' => 'required|string|max:2000',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'patient_id' => 'required',
                'doctor_id' => 'required',
                'is_active' => 'required|boolean',
            ]
        );
    }

    public function messages(): array
    {
        return [
            'end_date.after_or_equal' => 'La fecha de finalización debe ser igual o posterior a la fecha de inicio',
        ];
    }

    public function attributes(): array
    {
        return array_merge(
            $this->photoAttributes(),
            $this->filesAttributes(),
            [
                'title' => 'título',
                'recomendation_type_id' => 'tipo de recomendación',
                'priority' => 'prioridad',
                'content' => 'contenido',
                'start_date' => 'fecha de inicio',
                'end_date' => 'fecha de fin',
                'patient_id' => 'paciente',
                'doctor_id' => 'doctor',
                'is_active' => 'activo',
            ]
        );
    }
}
