<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Doctor\Catalogs\ExerciseType;
use Illuminate\Database\Seeder;

class ExerciseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ExerciseType::insert([
            [
                'name' => 'Cardio',
                'description' => 'Ejercicios para mejorar la resistencia cardiovascular.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fuerza',
                'description' => 'Ejercicios para fortalecer los músculos.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Flexibilidad',
                'description' => 'Ejercicios para mejorar la movilidad y elasticidad.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Equilibrio',
                'description' => 'Ejercicios para mejorar la estabilidad y coordinación.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
