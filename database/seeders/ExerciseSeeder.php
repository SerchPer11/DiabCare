<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Doctor\Catalogs\Exercise;

class ExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Exercise::create([
            'name' => 'Sentadillas',
            'exercise_type_id' => 1,
            'intensity' => 'media',
            'duration_minutes' => 10,
            'description' => 'Ejercicio básico para fortalecer piernas.',
            'calories_burned' => 80,
            'sets' => 3,
            'repetitions' => 15,
            'rest_seconds' => 60,
            'equipment' => 'Ninguno',
            'contraindications' => 'Evitar en caso de lesiones de rodilla.',
            'notes' => 'Mantener la espalda recta.',
            'is_active' => true,
        ]);

        Exercise::create([
            'name' => 'Flexiones',
            'exercise_type_id' => 1,
            'intensity' => 'alta',
            'duration_minutes' => 8,
            'description' => 'Ejercicio para fortalecer el tren superior.',
            'calories_burned' => 70,
            'sets' => 4,
            'repetitions' => 12,
            'rest_seconds' => 45,
            'equipment' => 'Ninguno',
            'contraindications' => 'Evitar en caso de problemas de hombro.',
            'notes' => 'No bajar demasiado el pecho.',
            'is_active' => true,
        ]);
    }
}
