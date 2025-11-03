<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class StartEmailWorker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:start-worker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicia el worker de correos en segundo plano para desarrollo';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando worker de correos...');
        $this->info('Los correos ahora se procesarán automáticamente');
        $this->info('Presiona Ctrl+C para detener');
        $this->newLine();

        // Ejecutar el worker con configuraciones optimizadas para desarrollo
        Artisan::call('queue:work', [
            '--verbose' => true,
            '--tries' => 3,
            '--timeout' => 60,
            '--sleep' => 3,
            '--max-jobs' => 1000,
            '--max-time' => 3600
        ]);

        return 0;
    }
}