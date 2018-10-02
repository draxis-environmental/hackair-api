<?php

namespace App\Http\Controllers\Api\v1;

use App\SocialActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Auth;
use App\Libraries\Responder;
use App\PhotoSky;
use MongoDB\BSON\UTCDateTime as MongoDate;
use App\Libraries\SunriseSunset;
use Illuminate\Support\Facades\URL;
use App\Libraries\Gamification;
use GuzzleHttp\Client;
use Event;
use App\Events\SocialActivityAdded;
use \Illuminate\Support\Facades\Lang;



class PhotoSkyController extends Controller
{

    public function create(Request $request) {

        $result = array();
        $failed = false;
        $props = $request->all();
        //\Log::info('Log: '.print_r($props['loc']));

        if (array_key_exists('location', $props)) {
            $props['loc'] = $props['location'];
        }

        if (array_key_exists('loc', $props)) {
            $props['loc'] = json_decode($props['loc'], true);
            if(!isset($props['loc']['coordinates']) || ( empty($props['loc']['coordinates'][0]) && empty($props['loc']['coordinates'][1]) ))
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
            $file_path = $request->file('file')->store('mobile_sky', 'uploads');
            $file_name = str_replace('mobile_sky/', '', $file_path);

            // Store thumb image in /public/uploads/mobile_sky/thumbs directory
            try {
                $thumb_file_path = 'public/uploads/mobile_sky/thumbs';
                $img = createImageThumb($request->file('file'), 1280, 768, $thumb_file_path, $file_name);
                $thumb_file_path = $img->dirname . '/' . $img->basename;
            } catch (Exception $e) {
                return Responder::ServerError('Could not save image.');
            }

            $photo = new PhotoSky;
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
                if ($photo->validatePhotoTimestamp($location['coordinates'], strtotime('now')) == false) {
                    $errors = [
                        'datetime' => [
                            'Our algorithm cannot estimate air pollution from sky photos taken close to the sunset time'
                        ]
                    ];
                    return Responder::ValidationError(Lang::get('aqi.sunrise'));
                    //$response = $this->ValidationError( $errors , Lang::get('aqi.sunrise'));
                    array_push($result, $response);
                    $failed = true;
                }
            }

            $properties['source_type'] = 'mobile';
            $properties['source_info'] = [
                'id' => 'mobile_' . md5($thumb_file_path),
                'image_url' => URL::to('uploads/' . $file_path, [], true),
                'file_path' => 'public/uploads/' . $file_path,
                'thumb_image_url' => URL::to(str_replace('public/', '', $thumb_file_path), [], true),
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
                $uploadSkyPhotoActivity = SocialActivity::firstOrCreate(['name' => 'UploadSkyPhoto']);
                $metadata = json_encode([
                    "sky_photo_id" => $photo->id,
                    'sky_photo_thumb' => str_replace('https','http',$properties['source_info']['thumb_image_url'])
                ]);
                Event::fire(new SocialActivityAdded($uploadSkyPhotoActivity->id, Auth::user(), $metadata));

                array_push($result, $response);
            } else {
                return Responder::ValidationError(Lang::get('aqi.failed_photo'));
                //$response = $this->ValidationError( $photo->errors(),Lang::get('aqi.failed_photo') );
                array_push($result, $response);

                $failed = true;
            }

        } else {

            return Responder::ValidationError(Lang::get('aqi.failed_photo'));

            //$response = $this->ValidationError('No file in upload','No file in upload');
            array_push($result, $response);

            $failed = true;
        }

        if ($failed) {
            // One or more of the validations has failed, so return a 419
            return Responder::ConflictError( $result );
        } else {
            //Notify service orchestrator by sending a POST request
            // try {
            //     $client = new Client();
            //     $url = env('ORCHESTRATOR_URL') . "/action";
            //     $res = $client->post($url, [
            //             'json' => [
            //                 'action' => 'finished',
            //                 'service' => 'MobilePhoto',
            //                 'msg' => []
            //             ]
            //         ]
            //     );
            //     $response = json_decode($res->getBody());
            // } catch (Exception $e) {
            //     //
            // }

            // Everything is ok
            return Responder::SuccessResponse( $result );
        }
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
            'message'=>$msg
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
          //  'loc' => 'required',
           // 'loc.coordinates' => 'required',
           // 'loc.coordinates.0' => 'required',
           // 'loc.coordinates.1' => 'required',
            'file' => 'required'
        ];

        /*return [
            'file' => 'required'
        ];*/
    }

}
