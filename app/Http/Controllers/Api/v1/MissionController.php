<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libraries\Responder;
use App\Mission;
use App\User;

class MissionController extends Controller
{
    /**
     * Retrieve all missions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $status = $request->input('status');
        $missions = [];
            
        switch($status) {
            case 'new':
                $new = Mission::getNew();
                array_push($missions, $new);
                break;
            case 'available':
                $missions = Mission::getAvailable();
                break;
            case 'completed':
                $missions = Mission::getCompleted();
                break;
            default:
                $missions = $all_missions_query->get();
        }

        return Responder::SuccessResponse( $missions );
    }

    /**
     * Retrieve the specified mission.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $mission = Mission::find($id);

        if ($mission) {
            return Responder::SuccessResponse( $mission );
        } else {
            return Responder::NotFoundError();
        }
    }
}
