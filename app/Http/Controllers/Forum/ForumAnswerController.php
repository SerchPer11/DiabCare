<?php

namespace App\Http\Controllers\Forum;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Forum\StoreForumAnswerRequest;
use App\Models\Forum\ForumAnswer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Forum\Forum;
use App\Models\User;
use App\Mail\ForumAnswerReceived;
use App\Mail\ForumNewActivity;

class ForumAnswerController extends Controller
{
    public function store(StoreForumAnswerRequest $request)
    {
        $answers = ForumAnswer::where('forum_id', $request->forum_id)
            ->count();
        if ($answers >= 3) {
            return redirect()->back()
                ->with('error', 'No se pueden agregar más respuestas a esta discusión.');
        }

        $newAnswer = null;
        
        DB::Transaction(function () use ($request, &$newAnswer) {
            $answer = new ForumAnswer();
            $answer->forum_id = $request->forum_id;
            $answer->user_id = Auth::user() ? Auth::user()->id : null;
            $answer->answer = $request->answer;
            $answer->save();

            $newAnswer = $answer->load(['forum.user', 'forum.category', 'user']);

            $answers = ForumAnswer::where('forum_id', $request->forum_id)
                ->count();
            if ($answers >= 3) {
                Forum::where('id', $request->forum_id)
                    ->update(['forum_status_id' => 2]);
            }
        });

        // Enviar correos después de la transacción exitosa
        if ($newAnswer) {
            $this->sendForumNotifications($newAnswer);
        }
        
        return redirect()->back()
            ->with('success', 'Respuesta agregada correctamente.');
    }

    /**
     * Envía las notificaciones por correo cuando se agrega una nueva respuesta al foro
     */
    private function sendForumNotifications(ForumAnswer $forumAnswer)
    {
        Log::info('Iniciando envío de notificaciones del foro', [
            'forum_id' => $forumAnswer->forum_id,
            'answer_id' => $forumAnswer->id,
            'responder_id' => $forumAnswer->user_id
        ]);

        try {
            // 1. Notificar al autor original de la pregunta
            $originalAuthor = $forumAnswer->forum->user;
            
            Log::info('Datos del autor original', [
                'original_author_id' => $originalAuthor ? $originalAuthor->id : null,
                'original_author_email' => $originalAuthor ? $originalAuthor->email : null,
                'responder_id' => $forumAnswer->user_id,
                'should_notify' => $originalAuthor && $originalAuthor->id !== $forumAnswer->user_id
            ]);
            
            if ($originalAuthor && $originalAuthor->id !== $forumAnswer->user_id) {
                Log::info('Enviando correo al autor original', ['email' => $originalAuthor->email]);
                Mail::to($originalAuthor->email)->send(new ForumAnswerReceived($forumAnswer));
                Log::info('Correo enviado al autor original');
                
                // Pequeño delay para evitar límites de Mailtrap
                sleep(1);
            }

            // 2. Obtener todos los usuarios que han participado en esta conversación
            $participantIds = ForumAnswer::where('forum_id', $forumAnswer->forum_id)
                ->where('user_id', '!=', $forumAnswer->user_id) // Excluir al que acaba de responder
                ->pluck('user_id')
                ->unique()
                ->toArray();

            // También excluir al autor original (ya se le envió el correo específico)
            $participantIds = array_filter($participantIds, function($userId) use ($originalAuthor) {
                return $userId !== $originalAuthor->id;
            });

            // 3. Enviar notificaciones de nueva actividad a los participantes
            if (!empty($participantIds)) {
                $participants = User::whereIn('id', $participantIds)->get();
                
                Log::info('Enviando correos de nueva actividad', [
                    'participant_count' => $participants->count(),
                    'participant_ids' => $participantIds
                ]);
                
                foreach ($participants as $participant) {
                    Log::info('Enviando correo de nueva actividad', ['email' => $participant->email]);
                    Mail::to($participant->email)->send(new ForumNewActivity($forumAnswer));
                    Log::info('Correo de nueva actividad enviado');
                    // Pequeño delay para evitar límites de Mailtrap
                    sleep(1);
                }
            } else {
                Log::info('No hay participantes para notificar');
            }

            Log::info('Notificaciones del foro enviadas correctamente', [
                'forum_id' => $forumAnswer->forum_id,
                'answer_id' => $forumAnswer->id,
                'original_author_notified' => $originalAuthor ? true : false,
                'participants_notified' => count($participantIds)
            ]);

        } catch (\Exception $e) {
            Log::error('Error al enviar notificaciones del foro', [
                'forum_id' => $forumAnswer->forum_id,
                'answer_id' => $forumAnswer->id,
                'error' => $e->getMessage()
            ]);
            
            // No lanzamos la excepción para no interrumpir el flujo principal
            // El usuario verá que su respuesta se guardó correctamente
        }
    }
}
