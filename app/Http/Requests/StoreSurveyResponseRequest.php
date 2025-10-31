<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSurveyResponseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'answers' => 'required|array|min:1',
            'answers.*.survey_question_id' => 'required|exists:survey_questions,id',
            'answers.*.likert_value' => 'required|integer|min:1|max:5',
            'answers.*.comment' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'answers.required' => 'Debe responder al menos una pregunta.',
            'answers.*.survey_question_id.required' => 'ID de pregunta requerido.',
            'answers.*.survey_question_id.exists' => 'La pregunta especificada no existe.',
            'answers.*.likert_value.required' => 'Debe seleccionar una respuesta.',
            'answers.*.likert_value.min' => 'El valor debe ser entre 1 y 5.',
            'answers.*.likert_value.max' => 'El valor debe ser entre 1 y 5.',
        ];
    }

    protected function prepareForValidation()
    {
        // Agregar el survey_id desde la URL
        $this->merge([
            'survey_id' => $this->route('survey')->id
        ]);
    }
}
