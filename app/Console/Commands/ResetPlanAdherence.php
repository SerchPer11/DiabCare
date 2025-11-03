<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Plan;

class ResetPlanAdherence extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plans:reset-adherence {plan? : ID del plan a resetear}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset adherence data for a plan (useful for testing)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $planId = $this->argument('plan');
        
        if ($planId) {
            $plan = Plan::find($planId);
            if (!$plan) {
                $this->error("Plan with ID {$planId} not found.");
                return Command::FAILURE;
            }
            
            $this->resetPlan($plan);
        } else {
            $plans = Plan::all();
            
            if ($plans->isEmpty()) {
                $this->error("No plans found in the database.");
                return Command::FAILURE;
            }
            
            if ($this->confirm('Reset adherence for ALL plans?')) {
                foreach ($plans as $plan) {
                    $this->resetPlan($plan);
                }
            }
        }
        
        $this->info("Adherence data reset successfully!");
        return Command::SUCCESS;
    }
    
    private function resetPlan(Plan $plan)
    {
        $plan->update([
            'overall_adherence' => 0,
            'days_tracked' => 0,
            'last_tracked_date' => null,
        ]);
        
        // Recalcular días totales
        $plan->calculateTotalPlanDays();
        
        $this->line("Plan #{$plan->id}: {$plan->title} - adherence reset");
    }
}
