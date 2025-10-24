<?php

namespace Database\Seeders;

use App\Models\Doctor\DoctorProfile;
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

        // Crear usuario Admin
        User::create([
            'name' => 'Admin',
            'last_name' => 'General',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345678'),
            'phone' => '0987654321',
            'gender' => 'male',
        ]);
        
        // Crear usuario Doctor
        $doctor= User::create([
            'name' => 'Dr. Juan',
            'last_name' => 'Pérez',
            'email' => 'doctor@gmail.com',
            'password' => bcrypt('12345678'),
            'phone' => '5555555555',
            'gender' => 'male',
        ]);

        // Crear usuario Paciente
        $patient = User::create([
            'name' => 'María',
            'last_name' => 'González',
            'email' => 'paciente@gmail.com',
            'password' => bcrypt('12345678'),
            'phone' => '1111111111',
            'gender' => 'female',
        ]);

        

        // Crear perfil de paciente
        $patientProfile = PatientProfile::create([
            'weight' => 65,
            'height' => 170,
            'blood_type' => 'O+',
        ]);

        $patient->profileable()->associate($patientProfile);
        $patient->save();
    }
}
