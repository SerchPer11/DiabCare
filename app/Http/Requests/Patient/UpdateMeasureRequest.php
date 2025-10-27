<?php

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMeasureRequest extends FormRequest
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
            'patient_id' => ['required', 'exists:users,id'],
            'measure_type_id' => ['required', 'exists:measure_types,id'],
            'measured_at' => ['required', 'date', 'before_or_equal:today'],
            'hour_measured' => ['required', 'date_format:H:i', 'before_or_equal:now'],
            'value' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'min_value' => ['nullable', 'numeric', 'lte:max_value'],
            'max_value' => ['nullable', 'numeric', 'gte:min_value'],
            'range' => ['nullable', 'string'],
            'severity' => ['nullable', 'string'],
            'frequency' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'measure_type_id.required' => 'Los campos tipo de monitoreo y unidad de medida son obligatorios.',
            'measured_at.before_or_equal' => 'La fecha de medición no puede ser futura.',
            'hour_measured.before_or_equal' => 'La hora de medición no puede ser futura.',
            'min_value.lte' => 'El valor mínimo debe ser menor o igual al valor máximo.',
            'max_value.gte' => 'El valor máximo debe ser mayor o igual al valor mínimo.',
        ];
    }

    public function attributes(): array
    {
        return [
            'patient_id' => 'paciente',
            'measured_at' => 'fecha de medición',
            'hour_measured' => 'hora de medición',
            'value' => 'valor',
            'notes' => 'notas',
            'min_value' => 'valor mínimo',
            'max_value' => 'valor máximo',
            'range' => 'rango',
            'severity' => 'severidad',
            'frequency' => 'frecuencia',
        ];
    }
}
