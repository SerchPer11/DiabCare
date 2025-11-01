<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSurveyRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'instructions' => 'required|string|max:1000',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'questions' => 'required|array|min:1|max:50',
            'questions.*.question' => 'required|string|max:500',
            'questions.*.is_required' => 'sometimes|boolean',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'El título es obligatorio.',
            'description.required' => 'La descripción es obligatoria.',
            'instructions.required' => 'Las instrucciones para los pacientes son obligatorias.',
            'starts_at.required' => 'La fecha de inicio es obligatoria.',
            'ends_at.required' => 'La fecha de fin es obligatoria.',
            'ends_at.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
            'questions.required' => 'Debe incluir al menos una pregunta.',
            'questions.min' => 'Debe tener al menos una pregunta.',
            'questions.max' => 'No puede tener más de 50 preguntas.',
            'questions.*.question.required' => 'El texto de la pregunta es obligatorio.',
        ];
    }
}
