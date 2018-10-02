<?php

namespace App\Events;

class Gamification extends Event
{

    public function __construct($user, $action) {
        $this->user  = $user;
        $this->action  = $action;
    }
}
