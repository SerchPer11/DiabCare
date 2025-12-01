<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\AppointmentStatus;
use App\Models\User;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener usuarios con rol paciente y doctor
        $patient = User::role('patient')->first();
        $doctors = User::role('doctor')->get();
        $statusNames = ['programada', 'completada', 'cancelada'];
        $statuses = [];
        foreach ($statusNames as $name) {
            $status = AppointmentStatus::where('name', $name)->first();
            if ($status) {
                $statuses[] = $status->id;
            }
        }

        if ($patient && $doctors->count() > 0 && count($statuses) === 3) {
            $modalities = ['virtual', 'presencial'];
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
                $statusId = $statuses[($i - 1) % 3];
                $doctor = $doctors[($i - 1) % $doctors->count()];
                Appointment::create([
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'date' => now()->addDays($i)->toDateString(),
                    'time' => now()->setTime(rand(8, 17), rand(0, 1) ? '00' : '30')->format('H:i:s'),
                    'modality' => $modalities[array_rand($modalities)],
                    'reason' => $reasons[array_rand($reasons)],
                    'additional_notes' => rand(0, 1) ? 'Nota de prueba ' . $i : null,
                    'video_call_link' => rand(0, 1) ? 'https://meet.example.com/cita' . $i : null,
                    'appointment_status_id' => $statusId,
                ]);
            }
        }
    }
}