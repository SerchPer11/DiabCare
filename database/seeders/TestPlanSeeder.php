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
            Plan::create([
                'patient_id' => $patient->id,
                'plan_type_id' => $planType->id,
                'assigned_by' => $admin->id,
                'title' => 'Plan de prueba desde seeder',
                'description' => 'Este es un plan creado para verificar que la base de datos funciona correctamente',
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addDays(30)->format('Y-m-d'),
                'status' => 'activo',
            ]);
        }
    }
}