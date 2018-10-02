<?php

namespace App\Events;
use App\User;

class PasswordResetRequested extends Event
{
    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $user;
    public $token;

    public function __construct(User $user, $token)
    {
        $this->user  = $user;
        $this->token = $token;
    }
}
