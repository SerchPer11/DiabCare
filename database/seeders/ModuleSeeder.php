<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;
use PhpParser\Node\Expr\AssignOp\Mod;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //modulos de administrador
        Module::create([
            'name' => 'Módulo de seguridad',
            'description' => 'Módulos de seguridad y administración',
            'key' => 'security',
            'user_id' => 1,
        ]);

        Module::create([
            'name' => 'Módulo de administración',
            'description' => 'Módulo para la gestión administrativa',
            'key' => 'admin',
            'user_id' => 1,
        ]);
          

        //modulo de pacientes
        Module::create([
            'name' => 'Módulo de pacientes',
            'description' => 'Módulo para la gestión de pacientes',
            'key' => 'patient',
            'user_id' => 1,
        ]);

        //modulo de medicos
        Module::create([
            'name' => 'Módulo de médicos',
            'description' => 'Módulo para la gestión de médicos',
            'key' => 'doctor',
            'user_id' => 1,
        ]);

        Module::create([
            'name' => 'Módulo de seguimiento clínico',
            'description' => 'Módulo para la gestión del seguimiento clínico de los pacientes',
            'key' => 'activity-log',
            'user_id' => 1,
        ]);

        Module::create([
            'name' => 'Módulo de encuestas',
            'description' => 'Módulo para la gestión de encuestas',
            'key' => 'surveys',
            'user_id' => 1,
        ]);

        Module::create([
            'name' => 'Módulo de reportes',
            'description' => 'Módulo para la generación de reportes',
            'key' => 'reports',
            'user_id' => 1,
        ]);

        Module::create([
            'name' => 'Módulo general',
            'description' => 'Módulo para la gestión general admin y doctor',
            'key' => 'general',
            'user_id' => 1,
        ]);
    }
}
