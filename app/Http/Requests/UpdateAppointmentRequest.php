<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
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
            'patient_id' => 'sometimes|exists:users,id',
            'doctor_id' => 'sometimes|exists:users,id',
            'date' => 'sometimes|date',
            'time' => 'sometimes',
            'modality' => 'sometimes|in:Presencial,Virtual',
            'reason' => 'sometimes|string',
            'additional_notes' => 'nullable|string',
            'video_call_link' => 'nullable|url',
            'appointment_status_id' => 'sometimes|exists:appointment_statuses,id',
        ];
    }
}
