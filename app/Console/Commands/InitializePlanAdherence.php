<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Plan;

class InitializePlanAdherence extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plans:initialize-adherence {--force : Force update even if values already exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize adherence fields for existing plans';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Initializing adherence fields for existing plans...');
        
        $query = Plan::query();
        
        // Si no se fuerza, solo actualizar planes sin total_plan_days
        if (!$this->option('force')) {
            $query->where('total_plan_days', 0);
        }
        
        $plans = $query->get();
        $updated = 0;
        
        foreach ($plans as $plan) {
            try {
                // Calcular total de días del plan
                $plan->calculateTotalPlanDays();
                
                // Si no tiene adherencia calculada, inicializar
                if ($this->option('force') || $plan->overall_adherence == 0) {
                    $plan->updateAdherencePercentage();
                }
                
                $updated++;
                $this->line("Plan #{$plan->id}: {$plan->title} - {$plan->total_plan_days} días");
                
            } catch (\Exception $e) {
                $this->error("Error updating Plan #{$plan->id}: " . $e->getMessage());
            }
        }

        $this->info("\nSuccessfully updated {$updated} plans with adherence information.");

        if ($updated > 0) {
            $this->table(
                ['Status', 'Count'],
                [
                    ['Total plans updated', $updated],
                    ['Average plan duration', round($plans->avg('total_plan_days'), 1) . ' days'],
                    ['Plans with adherence > 0%', $plans->where('overall_adherence', '>', 0)->count()],
                ]
            );
        }
        
        return Command::SUCCESS;
    }
}
