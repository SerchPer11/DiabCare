<?php

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientPathologiesRequest extends FormRequest
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
            'diabetes' => 'boolean',
            'diabetes_type' => 'nullable|string|in:TP1,TP2,Gest|required_if:diabetes,true',
            'diabetes_diagnosis_date' => 'nullable|date|before:tomorrow|required_if:diabetes,true',
            'hypertension' => 'boolean',
            'hypertension_type' => 'nullable|string|in:I,II,III|required_if:hypertension,true',
            'hypertension_diagnosis_date' => 'nullable|date|before:tomorrow|required_if:hypertension,true',
            'obesity' => 'boolean',
            'obesity_type' => 'nullable|string|in:N,I,II,III,IV|required_if:obesity,true',
            'allergies' => 'boolean',
            'allergies_details' => 'nullable|string|max:200|required_if:allergies,true',
        ];
    }

    public function messages(): array
    {
        return [
            'required_if' => 'El campo :attribute es obligatorio cuando :other es verdadero.',
            'in' => 'El campo :attribute debe ser uno de los siguientes valores: :values.',
            'before' => 'El campo :attribute debe ser una fecha anterior a hoy.',
        ];
    }

    public function attributes(): array
    {
        return [
            'diabetes' => 'Diabetes',
            'diabetes_type' => 'Tipo de Diabetes',
            'diabetes_diagnosis_date' => 'Fecha de Diagnóstico de Diabetes',
            'hypertension' => 'Hipertensión',
            'hypertension_type' => 'Tipo de Hipertensión',
            'hypertension_diagnosis_date' => 'Fecha de Diagnóstico de Hipertensión',
            'obesity' => 'Obesidad',
            'obesity_type' => 'Tipo de Obesidad',
            'allergies' => 'Alergias',
            'allergies_details' => 'Detalles de Alergias',
        ];
    }
}
