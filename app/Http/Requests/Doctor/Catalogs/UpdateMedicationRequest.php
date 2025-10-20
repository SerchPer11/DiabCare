<?php

namespace App\Http\Requests\Doctor\Catalogs;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\PhotoRules;
use Illuminate\Validation\Rule;

class UpdateMedicationRequest extends FormRequest
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
                'name' => [
                    'required',
                    'string',
                    'max:100',
                    Rule::unique('medications')->ignore($this->route('medication')),
                ],
                'concentration' => 'required|integer',
                'indications' => 'required|string|max:150',
                'contraindications' => 'required|string|max:150',
                'medication_type_id' => 'required|exists:medication_types,id',
                'medication_presentation_id' => 'required|exists:medication_presentations,id',
                'medication_administration_id' => 'required|exists:medication_administrations,id',
                'unit_id' => 'required|exists:units,id',
            ]
        );
    }
}
