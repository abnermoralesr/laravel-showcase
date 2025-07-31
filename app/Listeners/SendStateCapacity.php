<?php

namespace App\Listeners;

use App\Events\StateCapacity;
use App\Models\State;
use App\Notifications\StateCapacityLimitReached;
use App\Support\AnonymousNotifiable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendStateCapacity implements ShouldQueue
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
    public function handle(StateCapacity $event): void
    {
        $notifiable = new AnonymousNotifiable();
        $notifiable->email = $event->email;

        $state = State::where('iso_code', $event->isoCode)->first();

        $notifiable->notify(new StateCapacityLimitReached($event->percentage, $state->name ?? null));
    }
}
