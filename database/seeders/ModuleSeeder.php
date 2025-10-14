<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear un usuario administrativo si no existe o usar el primer usuario
        $user = \App\Models\User::first() ?? \App\Models\User::create([
            'name' => 'System',
            'email' => 'system@circuloazul.com',
            'password' => bcrypt('password'),
        ]);

        Module::create([
            'name' => 'Módulo de seguridad',
            'description' => 'Módulos de seguridad y administración',
            'key' => 'security',
            'user_id' => $user->id,
        ]);

        Module::create([
            'name' => 'Módulo médico',
            'description' => 'Módulo de gestión médica y pacientes',
            'key' => 'medical',
            'user_id' => $user->id,
        ]);

        Module::create([
            'name' => 'Módulo de reportes',
            'description' => 'Módulo de reportes y estadísticas',
            'key' => 'reports',
            'user_id' => $user->id,
        ]);

        Module::create([
            'name' => 'Módulo de archivos',
            'description' => 'Módulo de gestión de archivos y documentos',
            'key' => 'files',
            'user_id' => $user->id,
        ]);

        Module::create([
            'name' => 'Módulo de configuración',
            'description' => 'Módulo de configuración del sistema',
            'key' => 'settings',
            'user_id' => $user->id,
        ]);
    }
}
