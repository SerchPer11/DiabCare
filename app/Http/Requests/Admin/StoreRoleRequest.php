<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
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
            'name' =>'required|string|max:255|unique:roles,name',
            'description' => 'required|string|max:1000',
            'guard_name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ];
    }

    public function attributes() : array
    {
        return [
            'name' => 'nombre del rol',
            'description' => 'descripción del rol',
            'guard_name' => 'guardia del rol',
            'permissions' => 'permisos del rol',
            'permissions.*' => 'permiso del rol',
        ];
    }
}
