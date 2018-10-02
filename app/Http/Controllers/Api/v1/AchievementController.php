<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Achievement;
use App\User;
use Event;
use App\Libraries\Responder;

class AchievementController extends Controller
{
    /**
     * Retrieve all achievements.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {	
        $user_id = (int) $request->input('user_id');

        if (empty($user_id)) {
            $achievements = Achievement::all();
        } else {
            $user = User::find($user_id);
            $achievements = $user->achievements;
        }

        return Responder::SuccessResponse( $achievements );
    }
}
