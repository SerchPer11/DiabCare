<?php

namespace Database\Seeders;

use App\Models\PlanType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $planTypes = [
            [
                'name' => 'alimentacion',
                'description' => 'Planes de alimentación y nutrición para control de diabetes',
                'is_active' => true,
            ],
            [
                'name' => 'actividad_fisica',
                'description' => 'Planes de actividad física y ejercicio para pacientes diabéticos',
                'is_active' => true,
            ]
        ];

        foreach ($planTypes as $planType) {
            PlanType::firstOrCreate(
                ['name' => $planType['name']],
                $planType
            );
        }
    }
}
