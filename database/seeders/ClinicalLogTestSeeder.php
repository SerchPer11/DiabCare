<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Doctor\ClinicalLogEntry;
use App\Models\User;
use Carbon\Carbon;

class ClinicalLogTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a doctor and patient
        $doctor = User::role('doctor')->first();
        $patient = User::role('patient')->first();

        if (!$doctor || !$patient) {
            $this->command->warn('No doctor or patient found. Please seed users first.');
            return;
        }

        // Create some test clinical log entries
        $entries = [
            [
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'event_type' => 'observation',
                'title' => 'Control rutinario de glucosa',
                'description' => 'Paciente presenta niveles de glucosa dentro del rango normal. Se observa buena adherencia al plan alimentario.',
                'notes' => 'Continuar con el régimen actual. Próxima cita en 2 semanas.',
                'event_datetime' => Carbon::now()->subDays(2),
            ],
            [
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'event_type' => 'medication_adjustment',
                'title' => 'Ajuste de dosis de insulina',
                'description' => 'Se aumenta la dosis de insulina basal de 10 unidades a 12 unidades debido a niveles elevados en ayunas.',
                'notes' => 'Monitorear glucosa en ayunas por 5 días y reportar resultados.',
                'event_datetime' => Carbon::now()->subDays(5),
            ],
            [
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'event_type' => 'incident',
                'title' => 'Episodio de hipoglucemia',
                'description' => 'Paciente reporta episodio de hipoglucemia (50 mg/dl) después del ejercicio. Se resolvió con ingesta de carbohidratos.',
                'notes' => 'Revisar horarios de ejercicio y ajustar colación pre-ejercicio.',
                'event_datetime' => Carbon::now()->subDays(7),
            ],
            [
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'event_type' => 'document',
                'title' => 'Resultados de laboratorio - HbA1c',
                'description' => 'Resultados de hemoglobina glicosilada: 7.2%. Se mantiene dentro del objetivo terapéutico.',
                'notes' => 'Excelente control. Continuar con el tratamiento actual.',
                'event_datetime' => Carbon::now()->subDays(10),
            ],
        ];

        foreach ($entries as $entryData) {
            ClinicalLogEntry::create($entryData);
        }

        $this->command->info('Clinical log test entries created successfully!');
    }
}