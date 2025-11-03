<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Forum\Forum;
use App\Mail\ForumAnswerReceived;

class SendForumDigest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forum:test-email {email : Correo electrónico para la prueba}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía correos de prueba para las notificaciones del foro';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        return $this->sendTestEmails();
    }

    /**
     * Envía correos de prueba para testing
     */
    private function sendTestEmails()
    {
        $this->info('Enviando correos de prueba del foro...');
        
        // Buscar un foro con respuestas para usar como ejemplo
        $forumWithAnswer = Forum::whereHas('answers')
            ->with(['answers.user', 'user', 'category'])
            ->first();
            
        if (!$forumWithAnswer) {
            $this->error('No se encontraron foros con respuestas para la prueba.');
            $this->line('Crea primero algunas preguntas y respuestas en el foro.');
            return 1;
        }

        $testEmail = $this->argument('email');
        
        if (!filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            $this->error('Correo electrónico no válido.');
            return 1;
        }

        try {
            // Enviar correo de prueba usando la primera respuesta
            $firstAnswer = $forumWithAnswer->answers->first();
            
            $this->info("Enviando correo de prueba a: {$testEmail}");
            $this->info("Usando foro: '{$forumWithAnswer->title}'");
            $this->info("Respuesta de: {$firstAnswer->user->name}");

            Mail::to($testEmail)->send(new ForumAnswerReceived($firstAnswer));
            
            $this->info('✅ Correo de prueba enviado exitosamente!');
            $this->line('Revisa tu bandeja de entrada.');

        } catch (\Exception $e) {
            $this->error("Error al enviar correo: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }
}
