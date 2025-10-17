<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreModuleRequest extends FormRequest
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
            'name' => 'required|string|max:100|unique:modules,name',
            'description' => 'required|string|max:255',
            'key' => 'required|string|max:100|unique:modules,key',
        ];
    }

    public function attributes() : array
    {
        return [
            'name' => 'nombre',
            'description' => 'descripción',
            'key' => 'clave',
        ];
    }
}
