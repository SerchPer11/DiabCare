<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Doctor\Recomendation;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewRecommendation extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Recomendation $recommendation)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        // Generamos el link de la recomendación
        $link = route('patient.recommendations.show', $this->recommendation->id);

        return [
            'message' => 'Tu médico te ha enviado una nueva recomendación.',
            'icon' => 'mdi-clipboard-text',
            // --- CAMBIO AQUÍ ---
            // Añadimos el ID de la notificación como query parameter
            'link' => $link . '?notif_id=' . $this->id
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $link = route('patient.recommendations.show', $this->recommendation->id);

        return new BroadcastMessage([
            'message' => 'Tu médico te ha enviado una nueva recomendación.',
            'icon' => 'mdi-clipboard-text',
            // --- CAMBIO AQUÍ ---
            'link' => $link . '?notif_id=' . $this->id
        ]);
    }
}
