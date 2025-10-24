<?php

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateProfileRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string', 'max:12'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'birthdate' => ['required', 'date', 'before_or_equal:-4 years'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed','required_with:current_password', Password::defaults()],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],

            'blood_type' => ['required', 'string', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'height' => ['required', 'numeric', 'min:0'],
            'weight' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'birthdate.before_or_equal' => 'Debes tener al menos 4 años para registrarte.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'last_name' => 'apellido',
            'email' => 'correo electrónico',
            'phone' => 'teléfono',
            'gender' => 'género',
            'password' => 'contraseña',
            'current_password' => 'contraseña actual',
            'birthdate' => 'fecha de nacimiento',
            'blood_type' => 'tipo de sangre',
            'height' => 'estatura',
            'weight' => 'peso',
        ];
    }
}
