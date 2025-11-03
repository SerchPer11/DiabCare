<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Appointment;
use Illuminate\Validation\Validator;

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
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'modality' => 'required|in:Presencial,Virtual',
            'reason' => 'required|string|max:500',
            'additional_notes' => 'nullable|string|max:1000',
            'video_call_link' => 'nullable|url',
            'appointment_status_id' => 'required|exists:appointment_statuses,id',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->date && $this->time && $this->doctor_id) {
                // Convertir la hora solicitada a un objeto Carbon para cálculos
                $requestedDateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $this->date . ' ' . $this->time);
                $startRange = $requestedDateTime->copy()->subMinutes(15);
                $endRange = $requestedDateTime->copy()->addMinutes(15);

                // Verificar conflictos de horario para el doctor (rango de 30 minutos)
                $conflictingDoctorAppointment = Appointment::where('doctor_id', $this->doctor_id)
                    ->where('date', $this->date)
                    ->whereNotIn('appointment_status_id', function($query) {
                        $query->select('id')
                              ->from('appointment_statuses')
                              ->whereIn('name', ['cancelada', 'no asistió']);
                    })
                    ->get()
                    ->filter(function ($appointment) use ($startRange, $endRange) {
                        $appointmentDateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $appointment->date . ' ' . $appointment->time);
                        return $appointmentDateTime->between($startRange, $endRange);
                    });

                if ($conflictingDoctorAppointment->isNotEmpty()) {
                    $conflictTime = $conflictingDoctorAppointment->first()->time;
                    $validator->errors()->add(
                        'time', 
                        "Ya existe una cita programada para este doctor a las {$conflictTime}. Debe haber al menos 30 minutos de diferencia entre citas."
                    );
                }

                // Verificar conflictos de horario para el paciente (rango de 30 minutos)
                $conflictingPatientAppointment = Appointment::where('patient_id', $this->patient_id)
                    ->where('date', $this->date)
                    ->whereNotIn('appointment_status_id', function($query) {
                        $query->select('id')
                              ->from('appointment_statuses')
                              ->whereIn('name', ['cancelada', 'no asistió']);
                    })
                    ->get()
                    ->filter(function ($appointment) use ($startRange, $endRange) {
                        $appointmentDateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $appointment->date . ' ' . $appointment->time);
                        return $appointmentDateTime->between($startRange, $endRange);
                    });

                if ($conflictingPatientAppointment->isNotEmpty()) {
                    $conflictTime = $conflictingPatientAppointment->first()->time;
                    $validator->errors()->add(
                        'time', 
                        "El paciente ya tiene una cita programada a las {$conflictTime}. Debe haber al menos 30 minutos de diferencia entre citas."
                    );
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'date.after_or_equal' => 'La fecha de la cita no puede ser anterior a hoy.',
            'time.date_format' => 'El formato de hora debe ser HH:MM (ejemplo: 14:30).',
            'reason.max' => 'El motivo de consulta no puede exceder 500 caracteres.',
            'additional_notes.max' => 'Las notas adicionales no pueden exceder 1000 caracteres.',
        ];
    }
}
