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
            $modalities = ['Virtual', 'Presencial'];
            $reasons = [
                'Consulta de control',
                'Chequeo general',
                'Revisión de resultados',
                'Seguimiento de tratamiento',
                'Consulta de síntomas',
                'Evaluación inicial',
                'Consulta de emergencia',
                'Revisión de medicamentos',
                'Consulta nutricional',
                'Control de glucosa',
            ];
            for ($i = 1; $i <= 20; $i++) {
                Appointment::create([
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'date' => now()->addDays($i)->toDateString(),
                    'time' => now()->setTime(rand(8, 17), rand(0, 1) ? '00' : '30')->format('H:i:s'),
                    'modality' => $modalities[array_rand($modalities)],
                    'reason' => $reasons[array_rand($reasons)],
                    'additional_notes' => rand(0, 1) ? 'Nota de prueba ' . $i : null,
                    'video_call_link' => rand(0, 1) ? 'https://meet.example.com/cita' . $i : null,
                    'appointment_status_id' => $scheduledStatus->id,
                ]);
            }
        }
    }
}
