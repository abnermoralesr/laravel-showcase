<?php

namespace App\Events;

use App\Models\RegisteredAgent;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StateCapacity
{
    use Dispatchable, SerializesModels;

    public string $percentage;
    public string $email;
    public string $isoCode;

    /**
     * Create a new event instance.
     */
    public function __construct(string $percentage, string $email, string $isoCode)
    {
        $this->percentage = $percentage;
        $this->email = $email;
        $this->isoCode = $isoCode;
    }
}
