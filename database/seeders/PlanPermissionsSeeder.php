<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PlanPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos para el módulo de planes (doctores)
        $doctorPermissions = [
            'doctor.plans.index',
            'doctor.plans.create',
            'doctor.plans.store',
            'doctor.plans.show',
            'doctor.plans.edit', 
            'doctor.plans.update',
            'doctor.plans.destroy',
            'doctor.plans.duplicate'
        ];

        // Crear permisos para pacientes (solo visualización y adherencia)
        $patientPermissions = [
            'patient.plans.index',
            'patient.plans.show',
            'patient.plans.record-adherence',
            'patient.plans.adherence-stats'
        ];

        // Crear todos los permisos
        $allPermissions = array_merge($doctorPermissions, $patientPermissions);

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Asignar permisos al rol doctor
        $doctorRole = Role::firstOrCreate(['name' => 'doctor']);
        $doctorRole->givePermissionTo($doctorPermissions);

        // Asignar permisos al rol admin (todos los permisos)
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo($allPermissions);

        // Asignar permisos al rol patient (solo visualización)
        $patientRole = Role::firstOrCreate(['name' => 'patient']);
        $patientRole->givePermissionTo($patientPermissions);
    }
}
