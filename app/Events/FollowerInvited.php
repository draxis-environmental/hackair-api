<?php

namespace App\Events;

class FollowerInvited extends Event
{
    /**
     * Create a new event instance.
     *
     */
    public function __construct($user, $email)
    {
        $this->user  = $user;
        $this->email  = $email;
    }
}
