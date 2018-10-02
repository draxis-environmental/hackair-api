<?php
/**
 * Created by PhpStorm.
 * User: Ap
 * Date: 02/11/16
 * Time: 17:42
 */

namespace App\Http\Controllers\Api\v1;

use Event;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Libraries\Responder;
use App\Sensor;
use App\User;
use App\Measurement;
use MongoDB\BSON\UTCDateTime as MongoDate;
use App\SocialActivity;
use App\Events\SocialActivityAdded;
use App\Events\Gamification;
use App\Libraries\MongoHelper;

class SensorController extends Controller
{
    /**
     * Retrieve sensors of a specific user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user_id = $request->input('user_id');
        $user = User::findorfail($user_id);

        if($user->id == Auth::id() || !$user->private) {

            if($user->id !=  Auth::id() )
                $sensors = Sensor::where('user_id', '=', $user->id)->select('id','name','user_id','access_key','created_at','updated_at','type','mac_address','floor')->get();
            else {
                $sensors = Sensor::where('user_id', '=', $user->id)->get();
                foreach($sensors as &$row) {
                    $last_measurement = Measurement::where('source_info.sensor.id', $row['id'])->orderBy('datetime', 'desc')->take(1)->get(['datetime']);
                    if (count($last_measurement) > 0) {
                        $last_measurement = $last_measurement[0];
                        $row['last_measurement_at'] = date( 'Y-m-d H:i:s', MongoHelper::getUnixTimestampFromMongoUTCDateTime($last_measurement['datetime']) );
                    } else {
                        $row['last_measurement_at'] = NULL;
                    }
                }
            }

            return Responder::SuccessResponse( $sensors );
        }
        else {
            return Responder::NotFoundError(); // profile is private

        }

    }

    /**
     * Retrieve the specified sensor.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sensor = Sensor::find($id);

        if ($sensor) {
            if (empty($sensor['user_id']) == true
                || Auth::id() != $sensor['user_id']) {
                return Responder::UnauthorizedError();
            }

            return Responder::SuccessResponse( $sensor );
        } else {
            return Responder::NotFoundError( $sensor );
        }
    }

    public function register(Request $request)
    {
        $payload = $request->all();
        $payload['user_id'] = Auth::user()->id;
        $validator = app('validator')->make($payload, $this->getValidationRules());

        if ($validator->fails()) {
            return Responder::ValidationError( $validator->errors() );
        } else {
            $sensor = new \App\Sensor;
            $sensor->name = $request->input('name');
            if ($request->has('location')) {
                $sensor->location = $request->input('location');
            }

            if ($request->has('location_type')) {
                $sensor->location_type = $request->input('location_type');
            }

            if($request->has('floor')) {
                $sensor->floor = $request->input('floor');
            }

            $sensor->type =  $request->input('type');
            if ($sensor->type == 'bleair') {
                $sensor->mac_address =  $request->input('mac_address');
            }
            $sensor->user_id = Auth::user()->id;
            $sensor->access_key = Auth::user()->id.''.sha1(round(microtime(true) * 1000));
            $sensor->save();

            // add this sensor registration as a social activity record
            $registerSensorActivity = SocialActivity::where('name', 'RegisterSensor')->first();
            Event::fire(new SocialActivityAdded($registerSensorActivity->id, Auth::user(), $sensor->id));
            Event::fire(new Gamification(Auth::user(),'CreateSensor'));

            return Responder::SuccessCreateResponse($sensor);
        }
    }

    public function refreshAccessKey()
    {
        $sensor = \App\Sensor::findOrFail(route_parameter('sensor_id'));

        if($sensor->user_id == Auth::user()->id)
        {
            $sensor->access_key = Auth::user()->id.''.sha1(round(microtime(true) * 1000));
            $sensor->save();
            return Responder::SuccessResponse($sensor);
        }
        else
            return Responder::ValidationError(array());

    }

    /**
     * Update the specified sensor.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $sensor = Sensor::find($id);

        if (Auth::id() != $sensor['user_id']) {
            return Responder::UnauthorizedError();
        }

        $payload = $request->all();
        $validator = app('validator')->make($payload, $this->getValidationRules($sensor));


        if ($validator->fails()) {
            return Responder::ValidationError( $validator->errors() );
        } else {
            $sensor->update($payload);

            // add this sensor update as a social activity record
            $updateSensorActivity = SocialActivity::firstOrCreate(['name' => 'UpdateSensor']);
            $metadata = json_encode([
                "sensor_id" => $sensor->id
            ]);
            Event::fire(new SocialActivityAdded($updateSensorActivity->id, Auth::user(), $metadata));

            return Responder::SuccessResponse( $sensor );
        }
    }

    /**
     * Delete the specified sensor.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sensor = Sensor::find($id);

        if ($sensor) {
            if (empty($sensor['user_id']) == true
                || Auth::id() != $sensor['user_id']) {
                return Responder::UnauthorizedError();
            }

            $sensor->delete();
            Event::fire(new Gamification(Auth::user(),'RemoveSensor'));

            return Responder::SuccessResponse( $sensor );
        } else {
            return Responder::NotFoundError( $sensor );
        }
    }

    public function arduinoMeasurement(Request $request) {
        // Retrieve sensor from postgresql using token

        $token = $request->header('Authorization');
        $sensor = \App\Sensor::where('access_key', $token)->firstOrFail();

        $params = $request->all();

        $timestamp = isset($params['timestamp']) ? $params['timestamp'] : date('Y-m-d\TH:i:s.000\Z');

        if(!isset($params['reading']))
            return Responder::ValidationError('Missing Reading property');

        // $geocoder = new Geocoder();
        // try {
        //     $result = $geocoder->reverse($sensor->location['coordinates'][1], $sensor->location['coordinates'][0]);
        //     $city = $geocoder->getCity($result);
        // } catch (Exception $e) {
        //     echo $e->getMessage();
        // }

        //  ** Gather sensor data
        $sensorData = array(
            'city'        => '',
            'loc'         => $sensor->location['geometry'],
            'datetime'=> new MongoDate(new \DateTime($timestamp)),
            'date_str'    => $timestamp,
            'source_type' => 'sensors_arduino',
            'source_info' => [
                'user'    => [
                    'id'        => $sensor->user_id,
                    'username'  => $sensor->user->username
                ],
                'sensor'  => [
                    'id'        => $sensor->id,
                    'battery'   => isset($params['battery']) ? (int) $params['battery'] : 0,
                    'tamper'    => isset($params['tamper'])  ? (int) $params['tamper']  : 0,
                    'error'     => isset($params['error'])   ? (int) $params['error']   : 0
                ]
            ]
        );

        $result = [];

        //  ** Iterate over $params->reading, Create new MeasurementSensorsArduino for each reading
        // TODO validation for reading
        foreach ( $params['reading'] as $key => $value ) {

            $pollutantData = array(
                'pollutant_q' => array(
                    'name'  => $key,
                    'value' => $value,
                    'unit'  => 'mg'
                ),
                'pollutant_i' => array(
                    'name'  => '',
                    'index' => ''
                )
            );

            $measurementData = array_merge($pollutantData, $sensorData);

            $measurement = new \App\MeasurementSensorsArduino();
            $measurement->transform($measurementData);


            $measurement->save();

            array_push($result, $measurement);
        }

        return Responder::SuccessCreateResponse($result);
    }

    public function bleairMeasurement(Request $request) {
        $user = Auth::user();
        $params = $request->all();
        $timestamp = isset($params['timestamp']) ? $params['timestamp'] : date('Y-m-d\TH:i:s.000\Z');

        if (isset($params['sensor_id']) == true) {
            $sensor = Sensor::findOrFail($params['sensor_id']);
        }
        $sensorId = isset($sensor) == true ? $sensor->id : null;

        //  ** Gather sensor data
        $sensorData = array(
            'loc'         => $params['location'],
            'datetime'    => new MongoDate(new \DateTime($timestamp)),
            'date_str'    => $timestamp,
            'source_type' => 'sensors_bleair',
            'source_info' => [
                'user'    => [
                    'id'        => $user->id,
                    'username'  => $user->username
                ],
                'sensor'  => [
                    'id'        => $sensorId
                ]
            ]
        );

        $result = [];

        //  ** Iterate over $params->reading, Create new MeasurementSensorsBleAir for each reading
        // TODO validation for reading
        foreach ( $params['reading'] as $key => $value ) {

            $pollutantData = array(
                'pollutant_q' => array(
                    'name'  => $key,
                    'value' => $value,
                    'unit'  => 'mg'
                ),
                'pollutant_i' => array(
                    'name'  => '',
                    'index' => ''
                )
            );

            $measurementData = array_merge($pollutantData, $sensorData);

            $measurement = new \App\MeasurementSensorsBleAir();
            $measurement->transform($measurementData);


            $measurement->save();

            array_push($result, $measurement);
        }

        return Responder::SuccessCreateResponse($result);
    }

    /**
     * Returns validation rules
     *
     * @param \App\Sensor $sensor used for unique validations on update
     * @return array
     */
    protected function getValidationRules(Sensor $sensor = null)
    {
        return [
           'name' => 'required|min:2',
           'user_id' => 'required',
           'type' => 'required|in:arduino,bleair,cots'
        ];
    }
}
