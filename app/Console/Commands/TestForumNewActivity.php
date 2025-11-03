<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Forum\Forum;
use App\Models\Forum\ForumAnswer;
use App\Models\User;
use App\Mail\ForumNewActivity;

class TestForumNewActivity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forum:test-new-activity {email : Correo electrónico para la prueba}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba específicamente el correo ForumNewActivity';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Probando correo ForumNewActivity...');
        
        $testEmail = $this->argument('email');
        
        if (!filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            $this->error('Correo electrónico no válido.');
            return 1;
        }

        // Buscar un foro con respuestas para usar como ejemplo
        $forumWithAnswer = Forum::whereHas('answers')
            ->with(['answers.user', 'user', 'category'])
            ->first();
            
        if (!$forumWithAnswer) {
            $this->error('No se encontraron foros con respuestas para la prueba.');
            $this->line('Crea primero algunas preguntas y respuestas en el foro.');
            return 1;
        }

        try {
            // Usar la primera respuesta como ejemplo
            $firstAnswer = $forumWithAnswer->answers->first();
            
            $this->info("Enviando correo de prueba a: {$testEmail}");
            $this->info("Usando foro: '{$forumWithAnswer->title}'");
            $this->info("Simulando nueva actividad de: {$firstAnswer->user->name}");

            // Enviar correo ForumNewActivity directamente
            Mail::to($testEmail)->send(new ForumNewActivity($firstAnswer));
            
            $this->info('Correo ForumNewActivity enviado exitosamente!');
            $this->line('Revisa tu bandeja de entrada.');

        } catch (\Exception $e) {
            $this->error("Error al enviar correo: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }
}
