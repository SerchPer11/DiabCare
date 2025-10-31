<?php

namespace Database\Seeders;

use App\Models\Patient\ClinicalLog;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClinicalLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ClinicalLog::create([
            'patient_id' => 3,
            'doctor_id' => 2,
            'loggable_type' => 'App\Models\Doctor\Recomendation',
            'loggable_id' => 1,
            'event_type' => 'created',
        ]);
        ClinicalLog::create([
            'patient_id' => 3,
            'doctor_id' => 2,
            'loggable_type' => 'App\Models\Appointment',
            'loggable_id' => 1,
            'event_type' => 'created',
        ]);
        ClinicalLog::create([
            'patient_id' => 3,
            'doctor_id' => 2,
            'loggable_type' => 'App\Models\Appointment',
            'loggable_id' => 2,
            'event_type' => 'created',
        ]);
    }
}
