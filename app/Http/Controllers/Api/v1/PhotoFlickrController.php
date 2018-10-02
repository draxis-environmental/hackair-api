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
use Auth;
use App\Libraries\Responder;
use App\PhotoFlickr;
use App\SocialActivity;
use App\Events\SocialActivityAdded;


class PhotoFlickrController extends Controller
{

    public function create(Request $request) {
        // Expects array of photos

        $photos = $request->all();
        $result = array();
        $failed = false;

        foreach ($photos as $key => $item) {
            $photo = new PhotoFlickr;
            $properties = array();
            foreach ($item as $attribute => $value) {
                $properties[$attribute] = $value;
            }

            $photo->transform($properties);

            if ($photo->validate($properties)) {
                $photo->save();
                $response = $this->SuccessResponse( $photo );

                array_push($result, $response);
            } else {
                $response = $this->ValidationError( $photo->errors() );
                array_push($result, $response);
                $failed = true;
            }

        }

        if ($failed) {
            // One or more of the validations has failed, so return a 419
            return Responder::ConflictError( $result );
        } else {
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

    private function ValidationError($data)
    {
        $response = [
        'code' => 400,
        'status' => 'error',
        'type'=>'validation',
        'data' => $data
        ];
        return $response;
    }

}
