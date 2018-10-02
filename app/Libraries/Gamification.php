<?php

namespace App\Libraries;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Achievement;
use App\AchievementUser;
use App\Action;
use App\ActionUser;
use App\Level;
use App\Mission;
use App\User;
use App\MissionUser;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\SocialActivity;
use App\Events\SocialActivityAdded;
use Event;

class Gamification
{
    /**
     * Process action.
     *
     * @param string $actionName
     * @param array $payload
     */
    public static function processAction($actionName, $payload = [])
    {	
        $action = Action::where('name', $actionName)->first();
        $user_id = Auth::id();
        $user = User::find($user_id);
        $now = Carbon::now();
        $missions = [];
        $pointsWon = 0;
        $levelUp = false;

        // Get all available missions that require the specific action
        $missions = Mission::where('action_id', $action->id)      
            ->where(function ($query) use ($now) {
                $query->whereNull('datetime_starts')->orWhere('datetime_starts', '<=', $now);
            })->where(function ($query) use ($now) {
                $query->whereNull('datetime_ends')->orWhere('datetime_ends', '>=', $now);
            })->get();

        $missions_completed_ids = $user->missions()
            ->pluck('mission_id')
            ->toArray();
        
        // TODO allow user to complete more than one missions through each action
        // Find not completed missions
        foreach($missions as $mission) {
            if (in_array($mission->id, $missions_completed_ids) == false) {
                $validatorClassName = 'App\\Validators\\' . $mission->type . 'Validator';
                if (class_exists($validatorClassName)) {
                    $validator = new $validatorClassName;
                    $restrictions = $mission->restrictions();
                    $validated = $validator::validate($payload, $restrictions);
                    if ($validated) {
                        // Mark mission as completed for the user
                        $user->missions()->attach($mission->id, ['created_at' => date('Y-m-d H:i:s')]);
                        $mission_won = $mission;

                        // Find points to be assigned to user
                        $pointsWon += (int) $mission->points;

                        // Assign achievement to user
                        $achievement = $mission->achievement;
                        if (empty($achievement) == false) {
                            $user->achievements()->attach($achievement->id, ['created_at' => date('Y-m-d H:i:s')]);
                            $achievement_won = $achievement;
                        }
                        break;
                    }
                }
            }
        }

        // Assume that only one achievement can be gained from each action - might need to reconsider?
       if (empty($achievement_won) == true) {
            // Get all achievements that require the specific action
            $achievements = Achievement::where('action_id', $action->id)->get();
            $achievements_won_ids = AchievementUser::where('user_id', $user->id)->pluck('achievement_id')->toArray();
            foreach($achievements as $achievement) {
                if (in_array($achievement->id, $achievements_won_ids) == false) {
                    $validatorClassName = 'App\\Validators\\' . $achievement->type . 'Validator';
                    if (class_exists($validatorClassName)) {
                        $validator = new $validatorClassName;
                        $restrictions = $achievement->restrictions();
                        $validated = $validator::validate($payload, $restrictions);
                        if ($validated) {
                            // Assign achievement to user
                            $user->achievements()->attach($achievement->id, ['created_at' => date('Y-m-d H:i:s')]);
                            $achievement_won = $achievement;
                            break;
                        }
                    }
                }
            }
       }

        $totalPoints = $user->points + $pointsWon;

        // Check if user levels up
        $newLevel = Level::where('points_from', '<=', $totalPoints)->where('points_to', '>=', $totalPoints)->first();
        if ($newLevel->id != $user->level_id) {
            $levelUp = true;
        }

        // Assign points and new level to user
        $user->points = $totalPoints;
        if ($levelUp === true) {
            $user->level_id = $newLevel->id;
        }
        $user->progress = floor(100 * ($user->points - $newLevel->points_from) / ($newLevel->points_to - $newLevel->points_from));
        $user->save();


        
        // log action for gamification
        $actionUser = new ActionUser();
        $actionUser->user_id = $user->id;
        $actionUser->action_id = $action->id;
        $actionUser->mission_id = isset($mission_won) == true ? $mission_won->id : null;
        $actionUser->achievement_id = isset($achievement_won) == true ? $achievement_won->id : null;
        $actionUser->points = $pointsWon;
        $actionUser->levelup = $levelUp;
        $actionUser->save();

        // log social activities
        if (isset($achievement_won) && $achievement_won) {
            // add this achievement as a social activity record
            $earnAchievementActivity = SocialActivity::firstOrCreate(['name' => 'EarnAchievement']);
            $metadata = json_encode([
                "achievement_id" => $achievement->id
            ]);
            Event::fire(new SocialActivityAdded($earnAchievementActivity->id, Auth::user(), $metadata));
        }
        if (isset($levelUp) && $levelUp) {
            // add this level up as a social activity record
            $earnLevelActivity = SocialActivity::firstOrCreate(['name' => 'EarnLevel']);
            $metadata = json_encode([
                "level_id" => $user->level_id
            ]);
            Event::fire(new SocialActivityAdded($earnLevelActivity->id, Auth::user(), $metadata));
        }

        return $actionUser;
    }
}
