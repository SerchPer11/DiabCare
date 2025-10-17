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
        
        $allPermissions = Permission::all();
        //$adminPermissions = Permission::whereIn('module_key', ['admin'])->get();
        $admin->syncPermissions($allPermissions);

        /*
        $doctor = Role::create([
            'name' => 'doctor',
            'description' => 'Doctor',
        ]);

        
        $doctor->givePermissionTo([
            'patients.index',

            
        ]);

        // Patient - Acceso limitado
        $patient = Role::create([
            'name' => 'patient',
            'description' => 'Paciente',
        ]);

        $patient->givePermissionTo([
            'appointments.view',
            'files.view',
        ]);*/
    }
}
