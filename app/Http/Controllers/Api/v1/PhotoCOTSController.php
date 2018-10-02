<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Event;
use App\Libraries\Responder;
use App\PhotoCOTS;
use MongoDB\BSON\UTCDateTime as MongoDate;
use Illuminate\Support\Facades\URL;
use App\Libraries\Gamification;
use GuzzleHttp\Client;
use App\SocialActivity;
use App\Events\SocialActivityAdded;
use App\Measurement;
use \Illuminate\Support\Facades\Lang;


class PhotoCOTSController extends Controller
{

    public function create(Request $request) {

        $result = array();
        $failed = false;
        $props = $request->all();

        //\Log::info('Log: '.print_r($props['loc']));

        //print_r(var_dump($props));

        //return Responder::SuccessResponse( $props );

        if (array_key_exists('location', $props)) {
            $props['loc'] = $props['location'];
        }

        if (array_key_exists('loc', $props)) {
            $props['loc'] = json_decode($props['loc'], true);
            if(!isset($props['loc']['coordinates']) || ( empty($props['loc']['coordinates'][0]) || empty($props['loc']['coordinates'][1]) ))
                return Responder::ValidationError(Lang::get('aqi.coordinates_error'));
        }
        else
            return Responder::ValidationError(Lang::get('aqi.failed_photo'));


        // Validate request payload
        $validator = app('validator')->make($props, $this->getValidationRules());
        if ($validator->fails()) {
            return Responder::ValidationError( Lang::get('aqi.failed_photo') );
        }

        if ($request->hasFile('file')){
            // $curDate = date('Y-m-d',time());
            unset($props['file']);
            // Store image in /public/uploads/mobile_sky directory
            $file_path = $request->file('file')->store('mobile_cots', 'uploads');
            $file_name = str_replace('mobile_cots/', '', $file_path);

            // Store thumb image in /public/uploads/mobile_sky/thumbs directory
            try {
                $thumb_file_path = 'public/uploads/mobile_cots/thumbs';
                $img = createImageThumb($request->file('file'), 1280, 768, $thumb_file_path, $file_name);
                $thumb_file_path = $img->dirname . '/' . $img->basename;
            } catch (Exception $e) {
                return Responder::ServerError('Could not save image.');
            }

            $photo = new PhotoCOTS;
            $properties = array();

            foreach ($props as $attribute => $value) {
                $properties[$attribute] = $value;
            }

            // Currently the app only sends the date and not the time
            // $timestamp = (empty($properties['datetime']) == false) ? strtotime($properties['datetime']) : time();
            $timestamp = time();
            $properties['datetime'] = new MongoDate(new \DateTime(date('Y-m-d\TH:i:s.000\Z', $timestamp)));

            if (array_key_exists('loc', $properties)) {
                $location = $properties['loc'];

            }

            $properties['source_type'] = 'sensors_cots';
            $properties['source_info'] = [
                'id' => 'cots_' . md5($thumb_file_path),
                'image_url' => URL::to('uploads/' . $file_path),
                'file_path' => 'public/uploads/' . $file_path,
                'thumb_image_url' => URL::to(str_replace('public/', '', $thumb_file_path)),
                'thumb_file_path' => $thumb_file_path,
                'user' => [
                    'id' => Auth::user()->id,
                    'username' => Auth::user()->username
                ]
            ];

            $photo->transform($properties);

            if ($photo->validate($properties)) {
                $photo->save();
                $response = $this->SuccessResponse( $photo );

                // Process action and assign points/achievements to user
               // $response['actionUser'] = Gamification::processAction('UploadPhoto', $request->all());

                // add this upload as a social activity record
                $uploadCotsPhotoActivity = SocialActivity::firstOrCreate(['name' => 'UploadCOTSPhoto']);
                $metadata = json_encode([
                    "cots_photo_id" => $photo->id
                ]);
                Event::fire(new SocialActivityAdded($uploadCotsPhotoActivity->id, Auth::user(), $metadata));

                array_push($result, $response);
            } else {
                return Responder::ValidationError(Lang::get('aqi.failed_photo'));

                //$response = $this->ValidationError( $photo->errors(),Lang::get('aqi.failed_photo')  );
                array_push($result, $response);

                $failed = true;
            }

        } else {
            return Responder::ValidationError(Lang::get('aqi.failed_photo'));

            //$response = $this->ValidationError(array(),Lang::get('aqi.failed_photo') );
            array_push($result, $response);

            $failed = true;
        }

        if ($failed) {
            // One or more of the validations has failed, so return a 419
            return Responder::ConflictError( $result );
        } else {
            //Notify service orchestrator by sending a POST request
             try {

                 $client = new Client();
                 $url = env('ORCHESTRATOR_URL') . "/action";
                 $res = $client->post($url, [
                         'json' => [
                             'action' => 'finished',
                             'service' => 'CotsPhoto',
                             'photo_id' => $photo->id
                         ]
                     ]
                 );
                 $response = json_decode($res->getBody());

             } catch (Exception $e) {
                 //
             }

            // Everything is ok
            return Responder::SuccessResponse( $response );
        }
    }

    public function getCotsMeasurements() {

        $filters['source_type'] = 'sensors_cots';
        $filters['source_info.user.id'] =  Auth::user()->id;

        $sortFields = ['datetime' => -1];
        $collection = Measurement::getRawMeasurements($filters, array(), array(), $sortFields, 0, 50);

        print_r($collection);
        die();

    }

    // Class-specific responders, used to report individual success/failure on batch request
    private function SuccessResponse($data)
    {
        $response = [
            'code' => 201,
            'status' => 'success',
            'count' => sizeof($data),
            'resources' => $data
        ];
        return $response;
    }

    private function ValidationError($data,$msg = null)
    {
        $response = [
            'code' => 400,
            'status' => 'error',
            'type'=>'validation',
            'data' => $data,
            'message' => $msg
        ];
        return $response;
    }

    /**
     * Returns validation rules
     *
     * @return array
     */
    protected function getValidationRules()
    {
        return [
           // 'loc' => 'required',
            //'loc.coordinates' => 'required',
            //'loc.coordinates.0' => 'required',
            //'loc.coordinates.1' => 'required',
            'file' => 'required'
        ];
    }

}
