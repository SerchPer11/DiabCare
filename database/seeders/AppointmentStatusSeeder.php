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
            ['name' => 'programada', 'description' => 'La cita está programada y pendiente.'],
            ['name' => 'completada', 'description' => 'La cita ha sido completada.'],
            ['name' => 'cancelada', 'description' => 'La cita ha sido cancelada.'],
            ['name' => 'no asistió', 'description' => 'El paciente no se presentó a la cita.'],

        ];

        foreach ($statuses as $status) {
            AppointmentStatus::create($status);
        }
    }
}
