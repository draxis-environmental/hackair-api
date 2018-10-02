<?php

namespace App\Http\Controllers\Api\v1;

use App\Libraries\Responder;
// use App\User;
// use App\Farm;
use Illuminate\Http\Request;
// use JWTAuth;
use DB;

/**
 * Class MapController
 * @package App\Http\Controllers
 */
class MapController extends \App\Http\Controllers\Controller
{

    /**
     * This controller handles the map rendering of a single farm using MapScript
     * Request should include the JWT token and farmId as parameters
     * @param Request $request
     */
    public function getMap(Request $request)
    {
        // $user = User::findOrFail(\Auth::user()->id);
        // $farmId = $request['farmId'];
        // $farm = Farm::findOrFail($farmId);

        // if ($farm->owner->id != $user->id)
        //     return Responder::NotFoundError();


        // Fetch its boundaries table ID
        #$farmBoundariesId = $farm->boundaries_id;

        // Form the where clause for the PostGIS query
       # $whereClause = "ST_Intersects(rast, (SELECT polygon FROM farm_boundaries WHERE id = ".$farmBoundariesId."))";

        // Get database connection string from config file
        $dbHost = env('DB_HOST', 'postgres');
        $dbPort = env('DB_PORT', 5432);
        $dbName = env('DB_DATABASE', 'hackair');
        $dbUser = env('DB_USERNAME', 'hackair');
        $dbPass = env('DB_PASSWORD', 'hackair');

        /* MapScript section */

        $req = ms_newowsrequestobj();
        $req->loadparams();

        ms_ioinstallstdouttobuffer();
        $oMap = ms_newMapobj("/mapserver/mapfile.map");

        /* !! DONT NEED THIS, AS IT IS ALL INCLUDED IN THE MAPFILE ITSELF !!
        $layer = ms_newLayerObj($oMap);
        $layer->set("name", "hackair");
        $layer->set("status", MS_ON);
        $layer->setConnectionType(MS_POSTGIS);
        $layer->set("data", "PG:host='".$dbHost."' port=".$dbPort." dbname='".$dbName."' user='".$dbUser."' password='".$dbPass."' schema='public' table='fused_data' column='rast' where='created=\'{$request['date']}\'"  );

        $layer->set("type", MS_LAYER_RASTER);
        $layer->setProjection("init=epsg:4326");
        $layer->set("template", "ttt");
        $layer->set("dump", "true");
        $layer->offsite->setRGB(0,0,0);
        */

        $oMap->owsdispatch($req);

        $contenttype = ms_iostripstdoutbuffercontenttype();

        if ($contenttype == 'image/png') {
            header('Content-type: image/png');
            header("Access-Control-Allow-Origin: *");
        }

        ms_iogetStdoutBufferBytes();
        ms_ioresethandlers();


   }

}
