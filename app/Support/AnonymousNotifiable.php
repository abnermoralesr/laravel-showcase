<?php

namespace App\Support;

use Illuminate\Notifications\Notifiable;

class AnonymousNotifiable
{
    use Notifiable;

    public string $email;

    public function routeNotificationForMail(): string
    {
        return $this->email;
    }
}
