<?php

namespace App\Listeners;

use App\Events\RecommendationCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\NewRecommendation;

class SendNewRecommendationNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RecommendationCreated $event): void
    {
        // El listener se encarga de notificar
        $event->patient->notify(new NewRecommendation($event->recommendation));
    }
}
