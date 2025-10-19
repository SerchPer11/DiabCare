<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
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
            'name'        => "required|string|max:50|unique:permissions,name",
            'guard_name'  => "required|string",
            'description' => "required|string",
            'module_key'  => "required|exists:modules,key",
        ];
    }
    public function attributes(): array
    {
        return [
            'name'        => "nombre",
            'description' => "descripción",
            'module_key'  => "nombre del módulo",
        ];
    }
}
