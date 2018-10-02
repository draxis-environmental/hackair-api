<?php
/**
 * Created by PhpStorm.
 * User: jimi
 * Date: 9/8/2017
 * Time: 11:17 πμ
 */

namespace App\Http\Controllers\Api\v1;

use App\Events\SocialActivityAdded;
use App\Events\Gamification;
use App\SocialActivity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Perception;
use App\User;
use Auth;
use Event;

//use Event;
use App\Libraries\Responder;

class PerceptionController extends Controller
{
    public function index(Request $request){

        $user_id = (int) $request->input('user_id');
        $perceptions = [];
        if (empty($user_id)) {
            $perceptions = Perception::with('user')->where('created_at','>',date('Y-m-d 00:00:00'))->orderBy('created_at', 'desc')->take(10)->get();
        } else {
            $perceptions = Perception::where('user_id', '=', $user_id)->with('user')->where('created_at','>',date('Y-m-d 00:00:00'))->orderBy('created_at', 'desc')->take(10)->get();
        }

        return Responder::SuccessResponse($perceptions);
    }

    public function create(Request $request){
        $payload = $request->all();
        $payload['user_id'] = Auth::user()->id;

        $perception = new Perception();

        $perception->user_id = $payload['user_id'];
        $perception->perception = $payload['perception'];
        $perception->location = $payload['location'];

        $perception->save();

        // add this perception as a social activity record
        $addPerceptionActivity = SocialActivity::firstOrCreate(['name' => 'AddPerception']);
        $metadata = json_encode([
            "perception_id" => $perception->id
        ]);
        Event::fire(new SocialActivityAdded($addPerceptionActivity->id, Auth::user(), $metadata));

        Event::file(new Gamification(Auth::user(),'AddYourPerception'));

        return Responder::SuccessCreateResponse($perception);

    }
}
