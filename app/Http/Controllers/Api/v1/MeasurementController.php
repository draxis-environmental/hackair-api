<?php
/**
 * Created by PhpStorm.
 * User: Ap
 * Date: 02/11/16
 * Time: 17:42
 */

namespace App\Http\Controllers\Api\v1;

use App\Sensor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libraries\Responder;
use App\Measurement;
use App\MeasurementFlickr;
use App\MeasurementSensorsArduino;
use App\MeasurementSensorsBleAir;
use App\MeasurementSensorsCOTS;
use App\Perception;
use App\Libraries\MongoHelper;
use \Illuminate\Support\Facades\Lang;

use MongoDB\BSON\UTCDateTime as MongoDate;


class MeasurementController extends Controller {

    public function create(Request $request) {

        $properties = $request->all();

        switch ($properties['source_type']) {
            case 'flickr':
                $measurement = new MeasurementFlickr;
                $measurement->transform($properties);
                break;

            case 'sensors_arduino':
                $measurement = new MeasurementSensorsArduino;
                $measurement->transform($properties);
                break;

            case 'sensors_bleair':
                $measurement = new MeasurementSensorsBleAir;
                $measurement->transform($properties);
                break;

            case 'sensors_cots':
                $measurement = new MeasurementSensorsCOTS;
                $measurement->transform($properties);
                break;

            default:
                return Responder::ClientInputError('Invalid source type attribute');
                break;
        }

        if ($measurement->validate($properties)) {

            $measurement->save();
            return Responder::SuccessResponse( $measurement );

        } else {

            return Responder::ValidationError( $measurement->errors() );

        }


    }

    public function search(Request $request) {

        ini_set ('max_execution_time', 1600 );
        ini_set('memory_limit','712M');

        $rules = [
            'timestampStart' => 'required'
        ];

        $payload = $request->all();
        $validator = app('validator')->make($payload, $rules);

        if ($validator->fails()) {
            return Responder::ValidationError( $validator->errors() );
        } else {
            $result = Measurement::findAll($payload);
            
            if ($request->has('type') && $request->input('type') == 'geojson') {
                return Responder::geoJsonResponse($result);
            }

            /**
             * This need
             */
            $extra = array();
            if (array_key_exists('start', $payload) == true 
                && array_key_exists('length', $payload) == true) {
                $allProperties = $payload;
                unset($allProperties['start']);
                unset($allProperties['length']);
                $allRecordsResult = Measurement::findAll($allProperties);
                $extra['recordsTotal'] = count($allRecordsResult);
                $extra['recordsFiltered'] = count($result);
            }

            return Responder::SuccessResponse($result, '', $extra);
        }
    }

    public function update(Request $request) {
        // TODO
    }

    public function delete(Request $request) {
        // TODO
    }

    public function getPublicAPIData(Request $request) {

        ini_set ('max_execution_time', 1600 );
        ini_set('memory_limit','912M');

        if(!$request->has('location') && !$request->has('access_key'))
            return Responder::ValidationError( Lang::get('aqi.wrong_public_api_parameters') );

        $numberofDaysBefore = env('PUBLIC_API_MAX_HISTORY',2);
        $daysBefore = date('Y-m-d  H:i:s', strtotime(" -$numberofDaysBefore day"));

        $validationRules = array( 'location' => 'nullable|min:3','access_key' => 'nullable|min:10'); // not usefull , However it may use security checks
        $validator = app('validator')->make($request->all(), $validationRules);

        if ($validator->fails()) {
            return Responder::ValidationError( Lang::get('aqi.wrong_public_api_parameters') );
        }

        $filters = [
            'datetime' => [
                '$gt' => new MongoDate(new \DateTime($daysBefore)),
                '$lt' => new MongoDate(new \DateTime(date('Y-m-d  H:i:s')))
            ]
        ];


        if($request->has('location')) {

            $coordinates = explode('|', $request->input('location'));

            if(count($coordinates) <= 1)
                return Responder::ValidationError( Lang::get('aqi.missing_bounded_box') );

            $location = coordinates_str_to_array($coordinates);
            $geometry = '$box';

            $filters['loc'] = ['$geoWithin' => [$geometry => $location]];

        }

        if ($request->has('access_key')) {

            $sensor = \App\Sensor::where('access_key', $request->input('access_key'))->first();

            if($sensor)
                $filters['source_info.sensor.id'] = $sensor->id;
            else
                return Responder::ValidationError( Lang::get('aqi.wrong_sensor_key') );

        }

        $fields = ['loc', 'pollutant_q', 'pollutant_i', 'datetime', 'date_str', 'created_at', 'source_type', 'source_info'];

        if($request->has('location'))
            $groupFields = [
                '_id' => '$source_info.sensor.id',
            ];
        else
            $groupFields = [
                '_id' => '$_id'
            ];

        $availableFields = array ('pollutant_q.name'=>1,'pollutant_q.value'=>1,'_id'=>0, 'loc' => 1 , 'pollutant_i' => 1, 'date_str' => 1, 'source_type' => 1, 'source_info.source' => 1 );

        foreach($fields as $f) {
            $groupFields[$f] = [
                '$first' => '$' . $f
            ];
        }


        $sortFields = ['date_str' => -1];

        $data = Measurement::getRawMeasurements($filters, $groupFields,$availableFields, $sortFields, 0, 5000);

        foreach($data as &$row) {
            // Return coordinates in lat, lng format.
            $coordinatesLatLng = array();
            $coordinatesLatLng[0] = $row->loc->coordinates[1];
            $coordinatesLatLng[1] = $row->loc->coordinates[0];
            $row->loc->coordinates = $coordinatesLatLng;
        }

        $response['measurements'] = $data;
        $response['units'] = array('pollutant_q' => 'micrograms/m3');

        return response()->json($response,200);

    }

    public function export(Request $request) {


        ini_set ('max_execution_time', 1600 );
        ini_set('memory_limit','912M');

        $sensor = Sensor::find($request->input('sensor_id'));

        $filters['source_type'] = 'sensors_'.$sensor->type;
        $filters['source_info.sensor.id'] = intval($request->input('sensor_id'));
        $filters['datetime'] = array('$gt' => new MongoDate(new \DateTime($request->input('start'))), '$lt' => new MongoDate(new \DateTime($request->input('end'))));

        $sortFields = ['datetime' => -1];

        $collections = Measurement::getRawMeasurements($filters, array(), array(), $sortFields, 0, 5000);


        header('Content-Disposition: attachment; filename="export_1.csv";');
        header('Content-Type: application/csv; charset=UTF-8');

        $out = fopen('php://output', 'w');
        fputcsv($out, array('Sensor_id','Source_Type','Date','Pollutant_Q_Name','Pollutant_Q_Value','Pollutant_Q_unit','Pollutant_I_Name','Pollutant_I_Index','location'));

        foreach($collections as $collection)
        {

            $sensorId = (isset($collection->source_info->sensor->id)) ? $collection->source_info->sensor->id : '-';
            $date = (isset( $collection->datetime)) ?  date('Y-m-d H:i:s',MongoHelper::getUnixTimestampFromMongoUTCDateTime($collection->datetime)) : '-';
            $sourceType = (isset( $collection->source_type)) ?  $collection->source_type : '-';
            $p_q_name =  (isset( $collection->pollutant_q->name)) ?  $collection->pollutant_q->name : '-';
            $p_q_value =  (isset( $collection->pollutant_q->value)) ?  $collection->pollutant_q->value : '-';
            $p_q_unit =  (isset( $collection->pollutant_q->unit)) ?  'micrograms/cubic meter' : '-';
            $p_i_name =  (isset( $collection->pollutant_i->name)) ?  $collection->pollutant_i->name : '-';
            //$p_i_index =  (isset( $collection->pollutant_i->index)) ?  $collection->pollutant_i->index : '-';

            if(isset($collection->pollutant_i->index))
                $p_i_index = ($collection->pollutant_i->index == 'perfect') ? 'very good' : $collection->pollutant_i->index;
            else
                $p_i_index = '-';

            $p_loc = (isset($collection->loc->coordinates)) ? $collection->loc->coordinates[1].','.$collection->loc->coordinates[0] : '-';

            fputcsv($out, array($sensorId,
                                $sourceType,
                                $date,
                                $p_q_name,
                                $p_q_value,
                                $p_q_unit,
                                $p_i_name,
                                $p_i_index,
                                $p_loc));
        }

        fclose($out);

    }

    public function index(Request $request) {
        $measurements = Measurement::all();

        return Responder::SuccessResponse($measurements);
    }

}
