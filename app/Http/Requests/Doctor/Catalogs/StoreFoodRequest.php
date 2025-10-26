<?php

namespace App\Http\Requests\Doctor\Catalogs;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\PhotoRules;

class StoreFoodRequest extends FormRequest
{
    use PhotoRules;
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
            [
                'name' => 'required|string|max:100',
                'food_group_id' => 'required|exists:food_groups,id',
                'calories' => 'required|numeric|min:0',
                'carbohydrates' => 'required|numeric|min:0',
                'protein' => 'required|numeric|min:0',
                'fats' => 'required|numeric|min:0',
                'fiber' => 'required|numeric|min:0',
                'description' => 'nullable|string|max:1000',
                'unit_id' => 'required|exists:units,id|required_with:portion_size',
                'portion_size' => 'required|numeric|min:0|required_with:unit_id',
            ]
        );
    }

    public function messages()
    {
        return [
            'unit_id.required_with' => 'La unidad es obligatoria cuando se especifica el tamaño de porción.',
            'portion_size.required_with' => 'El tamaño de porción es obligatorio cuando se especifica la unidad.',
        ];
    }

    public function attributes(): array
    {
        return array_merge(
            $this->photoAttributes(),
            [
                'name' => 'nombre',
                'food_group_id' => 'grupo alimenticio',
                'calories' => 'calorías',
                'carbohydrates' => 'carbohidratos',
                'proteins' => 'proteínas',
                'fats' => 'grasas',
                'fiber' => 'fibra',
                'description' => 'descripción',
                'unit_id' => 'unidad',
                'portion_size' => 'tamaño de porción',
            ]
        );
    }
}
