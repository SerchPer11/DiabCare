<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSurveyRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'instructions' => 'nullable|string|max:1000',
            'starts_at' => 'nullable|date|after_or_equal:today',
            'ends_at' => 'nullable|date|after:starts_at',
            'questions' => 'required|array|min:1|max:50',
            'questions.*.question' => 'required|string|max:500',
            'questions.*.is_required' => 'sometimes|boolean',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'El título es obligatorio.',
            'questions.required' => 'Debe incluir al menos una pregunta.',
            'questions.min' => 'Debe tener al menos una pregunta.',
            'questions.max' => 'No puede tener más de 50 preguntas.',
            'questions.*.question.required' => 'El texto de la pregunta es obligatorio.',
            'ends_at.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
        ];
    }
}
