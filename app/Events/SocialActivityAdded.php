<?php

namespace App\Events;

class SocialActivityAdded extends Event
{
    /**
     * Create a new event instance.
     *
     */
    public function __construct($activityId, $user, $object_metadata = null)
    {
        $this->activity_id  = $activityId;
        $this->user  = $user;
        $this->object_metadata = $object_metadata;
    }
}
