<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        //securityistrador
        
        // Vistas de subgrupos de securityistración
        Permission::create(['name' => 'security.view', 'guard_name' => 'web', 'description' => 'Ver securityistración', 'module_key' => 'security']);

        // modulos
        Permission::create(['name' => 'modules.index', 'guard_name' => 'web', 'description' => 'Ver módulos', 'module_key' => 'security']);
        Permission::create(['name' => 'modules.create', 'guard_name' => 'web', 'description' => 'Crear módulos', 'module_key' => 'security']);
        Permission::create(['name' => 'modules.edit', 'guard_name' => 'web', 'description' => 'Editar módulos', 'module_key' => 'security']);
        Permission::create(['name' => 'modules.delete', 'guard_name' => 'web', 'description' => 'Eliminar módulos', 'module_key' => 'security']);

        // permisos
        Permission::create(['name' => 'permissions.index', 'guard_name' => 'web', 'description' => 'Ver permisos', 'module_key' => 'security']);
        Permission::create(['name' => 'permissions.create', 'guard_name' => 'web', 'description' => 'Crear permisos', 'module_key' => 'security']);
        Permission::create(['name' => 'permissions.edit', 'guard_name' => 'web', 'description' => 'Editar permisos', 'module_key' => 'security']);
        Permission::create(['name' => 'permissions.delete', 'guard_name' => 'web', 'description' => 'Eliminar permisos', 'module_key' => 'security']);

        //roles
        Permission::create(['name' => 'roles.index', 'guard_name' => 'web', 'description' => 'Ver roles', 'module_key' => 'security']);
        Permission::create(['name' => 'roles.create', 'guard_name' => 'web', 'description' => 'Crear roles', 'module_key' => 'security']);
        Permission::create(['name' => 'roles.edit', 'guard_name' => 'web', 'description' => 'Editar roles', 'module_key' => 'security']);
        Permission::create(['name' => 'roles.delete', 'guard_name' => 'web', 'description' => 'Eliminar roles', 'module_key' => 'security']);

        // usuarios
        Permission::create(['name' => 'users.index', 'guard_name' => 'web', 'description' => 'Ver usuarios', 'module_key' => 'security']);
        Permission::create(['name' => 'users.create', 'guard_name' => 'web', 'description' => 'Crear usuarios', 'module_key' => 'security']);
        Permission::create(['name' => 'users.edit', 'guard_name' => 'web', 'description' => 'Editar usuarios', 'module_key' => 'security']);
        Permission::create(['name' => 'users.delete', 'guard_name' => 'web', 'description' => 'Eliminar usuarios', 'module_key' => 'security']);

        // catalogo médico

        // Vistas de subgrupos de catalogo médico
        Permission::create(['name' => 'medic.view', 'guard_name' => 'web', 'description' => 'Ver catálogo médico', 'module_key' => 'doctor']);

        Permission::create(['name' => 'doctor.catalogs.medications.index', 'guard_name' => 'web', 'description' => 'Ver medicamentos', 'module_key' => 'doctor']);
        Permission::create(['name' => 'doctor.catalogs.medications.create', 'guard_name' => 'web', 'description' => 'Crear medicamentos', 'module_key' => 'doctor']);
        Permission::create(['name' => 'doctor.catalogs.medications.edit', 'guard_name' => 'web', 'description' => 'Editar medicamentos', 'module_key' => 'doctor']);
        Permission::create(['name' => 'doctor.catalogs.medications.delete', 'guard_name' => 'web', 'description' => 'Eliminar medicamentos', 'module_key' => 'doctor']);

        Permission::create(['name' => 'doctor.catalogs.exercises.index', 'guard_name' => 'web', 'description' => 'Ver ejercicios', 'module_key' => 'doctor']);
        Permission::create(['name' => 'doctor.catalogs.exercises.create', 'guard_name' => 'web', 'description' => 'Crear ejercicios', 'module_key' => 'doctor']);
        Permission::create(['name' => 'doctor.catalogs.exercises.edit', 'guard_name' => 'web', 'description' => 'Editar ejercicios', 'module_key' => 'doctor']);
        Permission::create(['name' => 'doctor.catalogs.exercises.delete', 'guard_name' => 'web', 'description' => 'Eliminar ejercicios', 'module_key' => 'doctor']);
    }
}
