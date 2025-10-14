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
        //Administrador
        
        // Vistas de subgrupos de administración
        Permission::create(['name' => 'security.view', 'guard_name' => 'web', 'description' => 'Ver administración', 'module_key' => 'admin']);

        // modulos
        Permission::create(['name' => 'modules.index', 'guard_name' => 'web', 'description' => 'Ver módulos', 'module_key' => 'admin']);
        Permission::create(['name' => 'modules.create', 'guard_name' => 'web', 'description' => 'Crear módulos', 'module_key' => 'admin']);
        Permission::create(['name' => 'modules.edit', 'guard_name' => 'web', 'description' => 'Editar módulos', 'module_key' => 'admin']);
        Permission::create(['name' => 'modules.delete', 'guard_name' => 'web', 'description' => 'Eliminar módulos', 'module_key' => 'admin']);

        // permisos
        Permission::create(['name' => 'permissions.index', 'guard_name' => 'web', 'description' => 'Ver permisos', 'module_key' => 'admin']);
        Permission::create(['name' => 'permissions.create', 'guard_name' => 'web', 'description' => 'Crear permisos', 'module_key' => 'admin']);
        Permission::create(['name' => 'permissions.edit', 'guard_name' => 'web', 'description' => 'Editar permisos', 'module_key' => 'admin']);
        Permission::create(['name' => 'permissions.delete', 'guard_name' => 'web', 'description' => 'Eliminar permisos', 'module_key' => 'admin']);

        //roles
        Permission::create(['name' => 'roles.index', 'guard_name' => 'web', 'description' => 'Ver roles', 'module_key' => 'admin']);
        Permission::create(['name' => 'roles.create', 'guard_name' => 'web', 'description' => 'Crear roles', 'module_key' => 'admin']);
        Permission::create(['name' => 'roles.edit', 'guard_name' => 'web', 'description' => 'Editar roles', 'module_key' => 'admin']);
        Permission::create(['name' => 'roles.delete', 'guard_name' => 'web', 'description' => 'Eliminar roles', 'module_key' => 'admin']);

        // usuarios
        Permission::create(['name' => 'users.index', 'guard_name' => 'web', 'description' => 'Ver usuarios', 'module_key' => 'admin']);
        Permission::create(['name' => 'users.create', 'guard_name' => 'web', 'description' => 'Crear usuarios', 'module_key' => 'admin']);
        Permission::create(['name' => 'users.edit', 'guard_name' => 'web', 'description' => 'Editar usuarios', 'module_key' => 'admin']);
        Permission::create(['name' => 'users.delete', 'guard_name' => 'web', 'description' => 'Eliminar usuarios', 'module_key' => 'admin']);

        
    }
}
