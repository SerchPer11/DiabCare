<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Forum\Forum;
use App\Models\Forum\ForumAnswer;
use App\Models\User;
use App\Http\Controllers\Forum\ForumAnswerController;

class SimulateForumAnswer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forum:simulate-answer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simula la creación de una respuesta del foro para probar correos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Simulando conversación del foro con múltiples participantes...');
        
        // Obtener un foro existente
        $forum = Forum::with('user')->first();
        if (!$forum) {
            $this->error('No se encontraron foros en la base de datos.');
            return 1;
        }

        $this->info("Foro encontrado: '{$forum->title}'");
        $this->info("Autor original: {$forum->user->name} ({$forum->user->email})");

        // Obtener usuarios diferentes al autor para simular participantes
        $users = User::where('id', '!=', $forum->user_id)->take(3)->get();
        if ($users->count() < 2) {
            $this->error('Se necesitan al menos 2 usuarios adicionales para la simulación.');
            return 1;
        }

        // 1. Primera respuesta - esto enviará correo al autor original solamente
        $this->info("\n--- PRIMERA RESPUESTA ---");
        $firstResponder = $users[0];
        $this->info("Usuario que responde: {$firstResponder->name} ({$firstResponder->email})");
        
        $answer1 = new ForumAnswer();
        $answer1->forum_id = $forum->id;
        $answer1->user_id = $firstResponder->id;
        $answer1->answer = 'Primera respuesta de prueba - ' . now()->format('H:i:s');
        $answer1->save();
        
        $this->info("Primera respuesta creada con ID: {$answer1->id}");
        $answer1->load(['forum.user', 'forum.category', 'user']);
        
        // Enviar notificaciones para la primera respuesta
        $this->sendNotifications($answer1);
        
        $this->info("Esperando 3 segundos antes de la segunda respuesta...");
        sleep(3);

        // 2. Segunda respuesta - esto enviará correo al autor original Y al primer participante
        $this->info("\n--- SEGUNDA RESPUESTA ---");
        $secondResponder = $users[1];
        $this->info("Usuario que responde: {$secondResponder->name} ({$secondResponder->email})");
        
        $answer2 = new ForumAnswer();
        $answer2->forum_id = $forum->id;
        $answer2->user_id = $secondResponder->id;
        $answer2->answer = 'Segunda respuesta de prueba - ' . now()->format('H:i:s');
        $answer2->save();
        
        $this->info("Segunda respuesta creada con ID: {$answer2->id}");
        $answer2->load(['forum.user', 'forum.category', 'user']);
        
        // Enviar notificaciones para la segunda respuesta
        $this->sendNotifications($answer2);

        $this->info("\n¡Conversación simulada! Revisa los logs y tu bandeja de Mailtrap.");
        $this->info("Deberías ver:");
        $this->info("- Correos 'ForumAnswerReceived' al autor original");
        $this->info("- Correos 'ForumNewActivity' a participantes previos");
        
        return 0;
    }

    private function sendNotifications(ForumAnswer $answer)
    {
        // Simular el envío de correos usando el mismo método del controlador
        $controller = new ForumAnswerController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('sendForumNotifications');
        $method->setAccessible(true);
        $method->invoke($controller, $answer);
    }
}
