<?php
/**
 * Created by PhpStorm.
 * User: ap
 * Date: 17/12/2017
 * Time: 19:33
 */


namespace App\Listeners;
use App\Events\Gamification;
use App\Action;
use App\Level;


class SetPointsListener
{

    public function __construct() {

    }

    public function handle(Gamification $event) {

        $action  = $event->action;
        
        try {
            if(method_exists($this,$action)) {
                $this->$action($event);
            }
            else
                return false;
        }
        catch (\Exception $e) {
            return false;
        }

    }


    private function CreateSensor($event) {
        $this->addPoints($event);
    }
    private function RemoveSensor($event) {
        $this->removePoints($event);
    }

    private function UploadPhoto($event) {
        $this->addPoints($event);
    }

    private function RemovePhoto($event){
        $this->removePoints($event);
    }

    private function AddYourPerception($event) {
        $this->addPoints($event);
    }

    private function UpdateProfile($event) {
        $this->addPoints($event);
    }

    private function RemoveProfile($event) {
        $this->removePoints($event);
    }

    private function removePoints($event) {

        $action = Action::where('name',$event->action)->first();
        $user = $event->user;

        $newPoints = $user->points - $action->points;

        if($newPoints < 0)
            $user->points = 0;
        else
            $user->points = $newPoints;
        
        $user->save();
        $this->syncLevel($user);
    }

    private function addPoints($event) {

        $action = Action::where('name',$event->action)->first();
        $user = $event->user;
        $user->points = $user->points + $action->points;
        $user->save();
        $this->syncLevel($user);
    }

    private function syncLevel($user) {

        $newLevel = Level::where('points_from', '<=', $user->points)->where('points_to', '>=', $user->points)->first();
        if($newLevel) {
            if ($newLevel->id != $user->level_id) {
                $user->level_id = $newLevel->id;
                $user->save();
            }
        }


    }

}
