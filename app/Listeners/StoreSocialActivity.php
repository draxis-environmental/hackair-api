<?php

namespace App\Listeners;

use App\Events\SocialActivityAdded;
use App\UserSocialActivity;
use Carbon\Carbon;
use URL;

class StoreSocialActivity
{
    protected $event;

    /**
     * Create the event listener.
     **/
    public function __construct()
    {

    }

    public function handle(SocialActivityAdded $event)
    {
        $this->event = $event;

        $userActivity = UserSocialActivity::where('social_activity_id', $event->activity_id)
            ->where('user_id', $event->user->id)
            ->where('visible', 1)
            ->orderBy('updated_at', 'DESC')
            ->first();

        $this->storeNewActivity();

        // check if another instance of social activity already exists - Temporarily Disabled
       /* if (!$userActivity) {
            $this->storeNewActivity();
        } else {
            $interval = Carbon::now()->diffInHours($userActivity->updated_at);
            // if more than an hour has passed, store a new activity instance
            if ($interval >= 1) {
                $this->storeNewActivity();
            } else {
                // if not, increase the latest record counter and add extra metadata
                $userActivity->counter++;
                if ($event->object_metadata) {
                    $previous = json_decode($userActivity->object_metadata,true);
                    $new = json_decode($event->object_metadata,true);
                    $merged = array_merge_recursive($previous, $new);
                    $userActivity->object_metadata = json_encode($merged);
                }
                $userActivity->save();
            }
        } */

    }

    /**
     * Creates a new record of this social activity
     */
    private function storeNewActivity()
    {
        UserSocialActivity::create([
            'user_id' => $this->event->user->id,
            'social_activity_id' => $this->event->activity_id,
            'object_metadata' => $this->event->object_metadata
        ]);
    }
}
