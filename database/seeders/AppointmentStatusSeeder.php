<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AppointmentStatus;

class AppointmentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'programada', 'description' => 'La cita está programada y pendiente de aceptación del paciente.'],
            ['name' => 'aceptada', 'description' => 'La cita ha sido aceptada por el paciente.'],
            ['name' => 'en proceso', 'description' => 'La cita está en proceso de atención.'],
            ['name' => 'completada', 'description' => 'La cita ha sido completada exitosamente.'],
            ['name' => 'cancelada', 'description' => 'La cita ha sido cancelada.'],
            ['name' => 'reagendada', 'description' => 'La cita ha sido reagendada a otra fecha.'],
            ['name' => 'no asistió', 'description' => 'El paciente no se presentó a la cita.'],
        ];

        foreach ($statuses as $status) {
            AppointmentStatus::create($status);
        }
    }
}
