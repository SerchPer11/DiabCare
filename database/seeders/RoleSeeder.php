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
        $adminPermissions = Permission::whereIn('module_key', ['security', 'general'])->orWhere('name', 'like', 'clinical-logbook.%')->orWhere('module_key', ['reports'])->orWhere('module_key', ['surveys'])->get();
        $admin->syncPermissions($adminPermissions);

        
        $doctor = Role::create([
            'name' => 'doctor',
            'description' => 'Doctor',
        ]);
        $doctorPermissions = Permission::whereIn('module_key', ['doctor', 'general'])->orWhere('module_key', ['activity-log'])->orWhere('module_key', ['surveys'])->orWhere('module_key', ['reports'])->orWhere('module_key', ['surveys'])->get();
        $doctor->syncPermissions($doctorPermissions);

        // Patient - Acceso limitado
        $patient = Role::create([
            'name' => 'patient',
            'description' => 'Paciente',
        ]);

        $patientPermissions = Permission::whereIn('module_key', ['patient', 'general'])->orWhere('module_key', ['activity-log'])->orWhere('module_key', ['surveys'])->get();
        $patient->syncPermissions($patientPermissions);
        
    }
}
