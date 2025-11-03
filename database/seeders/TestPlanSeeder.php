<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;
use App\Models\User;
use App\Models\PlanType;

class TestPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patient = User::role('patient')->first();
        $planType = PlanType::first();
        $admin = User::role('admin')->first();

        if ($patient && $planType && $admin) {
            // Plan 1 - Plan básico sin adherencia
            $plan1 = Plan::create([
                'patient_id' => $patient->id,
                'plan_type_id' => 1,
                'assigned_by' => $admin->id,
                'title' => 'Plan de Alimentación - Mes 1',
                'description' => 'Plan nutricional básico para controlar niveles de glucosa',
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addDays(30)->format('Y-m-d'),
                'status' => 'activo',
                'overall_adherence' => 0,
                'days_tracked' => 0,
                'last_tracked_date' => null,
            ]);

            // Calcular y actualizar el total de días del plan
            $plan1->calculateTotalPlanDays();

            // Plan 2 - Plan con alguna adherencia previa (para testing)
            $plan2 = Plan::create([
                'patient_id' => $patient->id,
                'plan_type_id' => 2,
                'assigned_by' => $admin->id,
                'title' => 'Plan de Ejercicios - Mes 1',
                'description' => 'Plan de actividad física para mejorar la condición general',
                'start_date' => now()->subDays(5)->format('Y-m-d'),
                'end_date' => now()->addDays(25)->format('Y-m-d'),
                'status' => 'activo',
                'overall_adherence' => 16.67,
                'days_tracked' => 3,
                'last_tracked_date' => now()->subDays(1)->format('Y-m-d'),
            ]);

            // Calcular y actualizar el total de días del plan
            $plan2->calculateTotalPlanDays();
        }
    }
}