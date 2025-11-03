<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class QueueWorker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the queue worker for processing emails';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando el worker de colas para procesar correos...');
        $this->info('Presiona Ctrl+C para detener el worker');
        
        // Ejecutar el worker de colas
        $this->call('queue:work', [
            '--verbose' => true,
            '--tries' => 3,
            '--timeout' => 60
        ]);
    }
}