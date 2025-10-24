<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Appointment;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener usuarios con rol paciente y doctor
        $patient = \App\Models\User::role('patient')->first();
        $doctor = \App\Models\User::role('doctor')->first();
        $scheduledStatus = \App\Models\AppointmentStatus::where('name', 'programada')->first();

        if ($patient && $doctor && $scheduledStatus) {
            Appointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'date' => now()->addDay()->toDateString(),
                'time' => '10:00:00',
                'modality' => 'Virtual',
                'reason' => 'Consulta de control',
                'additional_notes' => 'Paciente solicita revisión de resultados.',
                'video_call_link' => 'https://meet.example.com/abc123',
                'appointment_status_id' => $scheduledStatus->id,
            ]);

            Appointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'date' => now()->addDays(2)->toDateString(),
                'time' => '15:30:00',
                'modality' => 'Presencial',
                'reason' => 'Chequeo general',
                'additional_notes' => null,
                'video_call_link' => null,
                'appointment_status_id' => $scheduledStatus->id,
            ]);
        }
    }
}
