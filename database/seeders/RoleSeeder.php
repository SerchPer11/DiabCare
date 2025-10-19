<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin - Acceso total
        $admin = Role::create([
            'name' => 'admin',
            'description' => 'Administrador',
        ]);
        
        //$adminPermissions = Permission::all();
        $adminPermissions = Permission::whereIn('module_key', ['security'])->get();
        $admin->syncPermissions($adminPermissions);

        
        $doctor = Role::create([
            'name' => 'doctor',
            'description' => 'Doctor',
        ]);
        $doctorPermissions = Permission::whereIn('module_key', ['doctor'])->get();
        $doctor->syncPermissions($doctorPermissions);

        // Patient - Acceso limitado
        $patient = Role::create([
            'name' => 'patient',
            'description' => 'Paciente',
        ]);

        $patientPermissions = Permission::whereIn('module_key', ['patient'])->get();
        $patient->syncPermissions($patientPermissions);
        
    }
}
