<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
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
            'patient_id' => 'required|exists:users,id',
            'doctor_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'time' => 'required',
            'modality' => 'required|in:Presencial,Virtual',
            'reason' => 'required|string',
            'additional_notes' => 'nullable|string',
            'video_call_link' => 'nullable|url',
            'appointment_status_id' => 'required|exists:appointment_statuses,id',
        ];
    }
}
