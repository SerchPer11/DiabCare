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

        Module::create([
            'name' => 'Módulo de seguridad',
            'description' => 'Módulos de seguridad y administración',
            'key' => 'security',
            'user_id' => 1,
        ]);
    }
}
