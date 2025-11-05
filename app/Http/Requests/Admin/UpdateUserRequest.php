<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'second_last_name' => 'nullable|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user->id),
            ],
            'phone' => 'nullable|string|max:12',
            'gender' => 'nullable|string|in:male,female,other',
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',

        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'last_name' => 'apellido',
            'second_last_name' => 'segundo apellido',
            'email' => 'correo electrónico',
            'phone' => 'teléfono',
            'gender' => 'género',
            'password' => 'contraseña',
            'roles' => 'roles',
            'roles.*' => 'rol',
        ];
    }
}
