<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
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
        $user = $this->user();
        $licenseRule = Rule::unique('doctor_profiles', 'license_number');
        if ($user->profileable) {
            $licenseRule->ignore($user->profileable->id);
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string', 'max:12'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed','required_with:current_password', Password::defaults()],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],

            'specialty_id' => ['required', 'exists:specialties,id'],
            'license_number' => ['required', 'string', $licenseRule],
            'titulation_date' => ['required', 'date'],
            'birthdate' => ['required', 'date'],

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
            'specialty_id' => 'especialidad',
            'license_number' => 'número de cédula',
            'titulation_date' => 'fecha de titulación',
            'birthdate' => 'fecha de nacimiento',
        ];
    }
}
