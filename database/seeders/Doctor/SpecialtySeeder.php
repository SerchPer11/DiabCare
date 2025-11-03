<?php

namespace Database\Seeders\Doctor;

use App\Models\Doctor\Specialty;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Doctor\DoctorProfile;
use Spatie\Permission\Models\Role;

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Specialty::insert([
            ['name' => 'Cardiología', 'description' => 'Estudio del corazón y sus enfermedades'],
            ['name' => 'Dermatología','description' => 'Estudio de la piel y sus enfermedades'],
            ['name' => 'Endocrinología', 'description' => 'Estudio de las glándulas endocrinas y sus trastornos'],
            ['name' => 'Gastroenterología', 'description' => 'Estudio del sistema digestivo y sus enfermedades'],
            ['name' => 'Hematología', 'description' => 'Estudio de la sangre y sus trastornos'],
            ['name' => 'Infectología', 'description' => 'Estudio de las enfermedades infecciosas'],
            ['name' => 'Nefrología', 'description' => 'Estudio de los riñones y sus enfermedades'],
            ['name' => 'Pediatría', 'description' => 'Estudio de la salud infantil'],
            ['name' => 'Psiquiatría', 'description' => 'Estudio de los trastornos mentales'],
            ['name' => 'Reumatología', 'description' => 'Estudio de las enfermedades reumáticas'],
            ['name' => 'Neurología', 'description' => 'Estudio del sistema nervioso y sus trastornos'],
            ['name' => 'Neumología', 'description' => 'Estudio de los pulmones y sus enfermedades'],
            ['name' => 'Oncología', 'description' => 'Estudio de los tumores y el cáncer'],
            ['name' => 'Oftalmología', 'description' => 'Estudio de los ojos y la visión'],
            ['name' => 'Ortopedia', 'description' => 'Estudio de los huesos y el sistema musculoesquelético'],
            ['name' => 'Radiología', 'description' => 'Uso de imágenes para diagnosticar enfermedades'],
        ]);

        $doctor = User::create([
            'name' => 'Juan',
            'last_name' => 'Pérez',
            'second_last_name' => 'Sanchéz',
            'email' => 'doctor@gmail.com',
            'password' => bcrypt('12345678'),
            'phone' => '5555555555',
            'gender' => 'male',
            'email_verified_at' => now(),
        ]);

        User::factory()->count(3)->doctor()->create();

        $doctorProfile = DoctorProfile::create([
            'specialty_id' => 1,
            'license_number' => 'DOC123456',
        ]);

        $role = Role::find(2);
        $doctor->assignRole($role);
        $doctor->profileable()->associate($doctorProfile);
        $doctor->save();
    }
}
