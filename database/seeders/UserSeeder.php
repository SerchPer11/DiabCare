<?php

namespace Database\Seeders;

use App\Models\Doctor\DoctorProfile;
use App\Models\Patient\PatientPathologies;
use App\Models\Patient\PatientProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear pacientes
        $patient = User::create([
            'name' => 'María',
            'last_name' => 'González',
            'second_last_name' => 'Hernández',
            'email' => 'paciente@gmail.com',
            'password' => bcrypt('12345678'),
            'phone' => '1111111111',
            'gender' => 'female',
            'birthdate' => '1990-05-15',
            'email_verified_at' => now(),
        ]);

        // Crear perfil de paciente
        $patientProfile = PatientProfile::create([
            'weight' => 65,
            'height' => 170,
            'blood_type' => 'O+',
        ]);

        PatientPathologies::create([
            'patient_profile_id' => 1,
            'diabetes' => 0,
            'hypertension' =>0,
            'obesity' => 0,
            'allergies'=>0,
        ]);

        $patient->profileable()->associate($patientProfile);
        $patient->save();

        User::factory(12)->patient()->create();
    }
}
