<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AssignSurveyPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear o encontrar los permisos específicos de encuestas si no existen
        $permissions = [
            // Permisos para doctores
            ['name' => 'doctor.surveys.index', 'description' => 'Ver encuestas (Doctor)', 'module_key' => 'doctor'],
            ['name' => 'doctor.surveys.create', 'description' => 'Crear encuestas', 'module_key' => 'doctor'],
            ['name' => 'doctor.surveys.edit', 'description' => 'Editar encuestas', 'module_key' => 'doctor'],
            ['name' => 'doctor.surveys.delete', 'description' => 'Eliminar encuestas', 'module_key' => 'doctor'],
            ['name' => 'doctor.surveys.results', 'description' => 'Ver resultados de encuestas', 'module_key' => 'doctor'],
            
            // Permisos para pacientes
            ['name' => 'patient.surveys.index', 'description' => 'Ver encuestas (Paciente)', 'module_key' => 'patient'],
            ['name' => 'patient.surveys.submit', 'description' => 'Responder encuestas', 'module_key' => 'patient'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name'], 'guard_name' => 'web'],
                $permission
            );
        }

        // Obtener los roles
        $doctorRole = Role::where('name', 'doctor')->first();
        $patientRole = Role::where('name', 'patient')->first();
        $adminRole = Role::where('name', 'admin')->first();

        if ($doctorRole) {
            // Doctores pueden crear, editar, eliminar encuestas y ver resultados
            $doctorRole->givePermissionTo([
                'doctor.surveys.index',
                'doctor.surveys.create', 
                'doctor.surveys.edit',
                'doctor.surveys.delete',
                'doctor.surveys.results'
            ]);
        }

        if ($patientRole) {
            // Pacientes solo pueden ver encuestas disponibles y responderlas
            $patientRole->givePermissionTo([
                'patient.surveys.index',
                'patient.surveys.submit'
            ]);
        }

        if ($adminRole) {
            // Administradores tienen todos los permisos
            $adminRole->givePermissionTo([
                'doctor.surveys.index',
                'doctor.surveys.create', 
                'doctor.surveys.edit',
                'doctor.surveys.delete',
                'doctor.surveys.results',
                'patient.surveys.index',
                'patient.surveys.submit'
            ]);
        }

        $this->command->info('Permisos de encuestas asignados exitosamente a los roles.');
    }
}
