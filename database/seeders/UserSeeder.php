<?php

namespace Database\Seeders;

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
        $admin = User::create([
            'name' => 'Admin',
            'last_name' => 'General',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345678'),
            'phone' => '0987654321',
            'gender' => 'male',
        ]);
        $admin->assignRole('admin');

        /*
        // Crear usuario Doctor
        $doctor = User::create([
            'name' => 'Dr. Juan',
            'last_name' => 'Pérez',
            'email' => 'doctor@gmail.com',
            'password' => bcrypt('password123'),
            'phone' => '5555555555',
            'gender' => 'male',
        ]);
        $doctor->assignRole('doctor');

        // Crear usuario Paciente
        $patient = User::create([
            'name' => 'María',
            'last_name' => 'González',
            'email' => 'paciente@gmail.com',
            'password' => bcrypt('password123'),
            'phone' => '1111111111',
            'gender' => 'female',
        ]);
        $patient->assignRole('patient');

        //$this->command->info('Usuarios con roles creados exitosamente!');*/
    }
}
