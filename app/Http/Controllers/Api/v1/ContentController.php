<?php
/**
 * Created by PhpStorm.
 * User: Ap
 * Date: 02/11/16
 * Time: 17:42
 */

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libraries\Responder;
use App\OutdoorActivity;
use App\UserGroup;
use \Illuminate\Support\Facades\Lang;



class ContentController extends Controller
{

    public function getRecommendationsContent(Request $request) {

        $content = array();
        $tmpActivities = OutdoorActivity::all();
        $tmpGroups = UserGroup::all();

        $allActivities = array();
        $allGroups = array();

        foreach($tmpActivities as $activity) {
            $obj = new \stdClass();
            $obj->id = $activity->id;
            $obj->name = Lang::get('profile.outdoor_activities.'.$activity->name);
            array_push($allActivities,$obj);
        }

        foreach($tmpGroups as $group) {
            $obj = new \stdClass();
            $obj->id = $group->id;
            $obj->name = Lang::get('profile.groups.'.$group->name);
            array_push($allGroups,$obj);
        }

        $content['activities'] = $allActivities;
        $content['groups'] = $allGroups;

        return Responder::SuccessResponse($content);


    }


}
