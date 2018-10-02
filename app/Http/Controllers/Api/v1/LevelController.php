<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libraries\Responder;
use App\Level;

class LevelController extends Controller
{
    /**
     * Retrieve all levels.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $levels = Level::orderBy('id','desc')->get();
        return Responder::SuccessResponse( $levels );
    }
}
