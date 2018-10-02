<?php

namespace App\Listeners;

use App\Events\Gamification;
use App\ActionUser;
use App\Action;

class LogGamificationActionsListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    public function handle(Gamification $event) {

        // New gamification started at 18-12-2017

        $action = Action::where('name',$event->action)->first();

        try {
            $actionUser = new ActionUser();
            $actionUser->user_id = $event->user->id;
            $actionUser->action_id = $action->id;
            $actionUser->mission_id = null;
            $actionUser->achievement_id = null;
            $actionUser->points = 0;
            $actionUser->levelup = false;
            $actionUser->save();

        } catch (\Exception $e) {
           return false;
        }
    }
}
