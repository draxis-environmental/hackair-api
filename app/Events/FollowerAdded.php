<?php

namespace App\Events;

class FollowerAdded extends Event
{
    /**
     * Create a new event instance.
     *
     */
    public function __construct($userId, $followerId)
    {
        $this->userId  = $userId;
        $this->followerId = $followerId;
    }
}
