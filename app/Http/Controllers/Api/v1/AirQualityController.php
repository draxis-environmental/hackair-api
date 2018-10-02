<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libraries\Responder;
use App\Libraries\AirQualityIndex;
use \Illuminate\Support\Facades\Lang;

class AirQualityController extends Controller
{
    /**
     * Retrieve air quality for the specified location.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAQ(Request $request)
    {
        ini_set ('max_execution_time', 1600 );
        ini_set('memory_limit','1800M');
        $lon = $request->input('lon');
        $lat = $request->input('lat');
        $dateStart = $request->input('dateStart');
        $dateStart = empty($dateStart) == false ? $dateStart : date('Y-m-d');
        $dateEnd = $request->input('dateEnd');
        $dateEnd = empty($dateEnd) == false ? $dateEnd : date('Y-m-d');

        if (empty($lon) || empty($lat)) {
            return Responder::BadRequestError();
        }

        $aqi = AirQualityIndex::getAQI($lon, $lat, $dateStart, $dateEnd);

        if (empty($aqi) == true) {
            $params = [ 'message' => Lang::get('responses.aq_not_found') ];
            return Responder::NotFoundError($params);
        }

        return Responder::SuccessResponse( $aqi );
    }
}
