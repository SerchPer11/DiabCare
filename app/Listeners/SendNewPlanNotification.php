<?php

namespace App\Listeners;

use App\Events\PlanAssigned;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\NewPlanAssigned;

class SendNewPlanNotification
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
    public function handle(PlanAssigned $event): void
    {
        // El listener se encarga de notificar
        $event->patient->notify(new NewPlanAssigned($event->plan));
    }
}
