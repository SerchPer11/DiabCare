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
        $patients = User::role('patient')->get();
        $planTypes = PlanType::all();
        $admin = User::role('admin')->first();

        if ($patients->count() && $planTypes->count() && $admin) {
            $adherences = [0, 16.67, 50, 75, 100];
            // Only use allowed status values for plans
            $statuses = ['activo', 'completado', 'cancelado'];
            foreach ($patients as $patient) {
                for ($i = 0; $i < 2; $i++) {
                    $planType = $planTypes->random();
                    $adherence = $adherences[array_rand($adherences)];
                    $status = $statuses[array_rand($statuses)];
                    $daysTracked = $adherence > 0 ? rand(1, 10) : 0;
                    $plan = Plan::create([
                        'patient_id' => $patient->id,
                        'plan_type_id' => $planType->id,
                        'assigned_by' => $admin->id,
                        'title' => 'Plan ' . $planType->name . ' ' . ($i + 1),
                        'description' => 'Sample plan for testing',
                        'start_date' => now()->addDays(rand(-30, 0)),
                        'end_date' => now()->addDays(rand(1, 30)),
                        'status' => $status,
                        'overall_adherence' => $adherence,
                        'days_tracked' => $daysTracked,
                        'last_tracked_date' => $daysTracked > 0 ? now()->subDays(rand(0, 5))->format('Y-m-d') : null,
                    ]);
                    if (method_exists($plan, 'calculateTotalPlanDays')) {
                        $plan->calculateTotalPlanDays();
                    }
                }
            }
        }
    }
}