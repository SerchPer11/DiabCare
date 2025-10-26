<?php

namespace Database\Seeders\Doctor;

use App\Models\Admin\Catalogs\RecomendationType;
use App\Models\Doctor\Recomendation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RecomendationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recomendationTypes = [
            ['name' => 'Nutricional', 'description' => 'Recomendaciones relacionadas con la nutrición y la dieta.'],
            ['name' => 'Ejercicio', 'description' => 'Recomendaciones sobre rutinas de ejercicio y actividad física.'],
            ['name' => 'Medicamentos', 'description' => 'Instrucciones sobre la administración de medicamentos.'],
            ['name' => 'Hábitos de Sueño', 'description' => 'Consejos para mejorar los hábitos de sueño y descanso.'],
            ['name' => 'Salud Mental', 'description' => 'Recomendaciones para el bienestar emocional y mental.'],
        ];

        foreach ($recomendationTypes as $type) {
            RecomendationType::create($type);
        }

        Recomendation::create([
            'title' => 'Dieta Balanceada',
            'recomendation_type_id' => 1,
            'priority' => 3,
            'content' => 'Incluir más frutas y verduras en la dieta diaria.',
            'start_date' => now(),
            'end_date' => now()->addMonths(1),
            'patient_id' => 3,
            'doctor_id' => 2,
            'is_active' => true,
        ]);
    }
}
