<?php
/**
 * Created by PhpStorm.
 * User: ap
 * Date: 17/12/2017
 * Time: 18:23
 */

namespace App\Listeners;
use App\Events\Gamification;
use App\Libraries\Achievements;
use App\AchievementUser;

use App\Events\SocialActivityAdded;
use App\SocialActivity;



class EarnAchievementListener
{

    public function __construct() {}

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

        $numOfSensors = $event->user->sensors()->count();

        if($numOfSensors == 3)
            $achievement = Achievements::$Hackair_Hero;
        elseif($numOfSensors == 1)
            $achievement = Achievements::$A_Hackair_Masterpiece;
        else
            return false;

        $this->awardAchievement($event->user,$achievement);

    }

    private function AddYourPerception($event) {
        $this->awardAchievement($event->user,Achievements::$How_Is_Your_Life_Today);
    }

    private function UpdateProfile($event) {
        $this->awardAchievement($event->user,Achievements::$A_Health_Wathcer);
    }

    private function UploadPhoto($user) {}

    private function loginToPlatform($user) {}

    private function postAQuestion($user) {}

    private function postAnAnswer($user) {}

    private function inviteFriends($user) {}

    private function AddMeasurements($user) {}

    private function awardAchievement($user,$achievementId) {

        $achievementUser = AchievementUser::Where('user_id',$user->id)->where('achievement_id',$achievementId)->first();

        if(!$achievementUser) {
            $achievementUser = new AchievementUser();
            $achievementUser->user_id = $user->id;
            $achievementUser->achievement_id = $achievementId;
            $achievementUser->save();

          //  $registerSensorActivity = SocialActivity::where('name', 'EarnAchievement')->first();
          //  Event::fire(new SocialActivityAdded($registerSensorActivity->id, $user, $achievementId));
        }

    }


}
