<?php

namespace App\Listeners;

use App\Events\RegisteredAgentAssigned;
use App\Models\RegisteredAgent;
use App\Notifications\NewCompanyAssigned;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendRegisteredAgentNotification implements ShouldQueue
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
    public function handle(RegisteredAgentAssigned $event): void
    {
        $agent = RegisteredAgent::find($event->company->registered_agent_id);

        if (!$agent) {
            return;
        }

        $agent->notify(new NewCompanyAssigned($event->company));
    }
}
