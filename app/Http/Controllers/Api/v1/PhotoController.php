<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libraries\Responder;
use App\Photo;
use App\Measurement;
use Illuminate\Support\Facades\Auth;
use App\User;


class PhotoController extends Controller
{
    /**
     * Retrieve photos of a specific user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = User::findorfail($request->input('user_id'));

        if($user->id == Auth::id() || !$user->private) {

            $photos = Photo::where('source_info.user.id', '=', $user->id)->get();
            return Responder::SuccessResponse( $photos );

        }
        else
            return Responder::UnauthorizedError();
        
    }


    public function getAQI($photoId) {

        try {
            $photos = Measurement::where('source_info.user.id', '=',Auth::id() )->where('image_info.id',$photoId)->first();
            return Responder::SuccessResponse( $photos['pollutant_i'] );
        }
        catch (\Exception $e) {
            return Responder::ClientInputError($e->getMessage());
        }

    }

    /**
     * Delete the specified photo.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $photo = Photo::find($id);

        if (empty($photo['source_info']['user']) == true
            || empty($photo['source_info']['user']['id']) == true
            || Auth::id() != $photo['source_info']['user']['id']) {
            return Responder::UnauthorizedError();
        }
        if ($photo) {
            $photo->delete();
            return Responder::SuccessResponse( $photo );
        } else {
            return Responder::NotFoundError( $photo );
        }
    }

    /**
     * Generate dynamic image thumb.
     * 
     * @return \Illuminate\Http\Response
     */
    public function thumb(Request $request)
    {
        $url = $request->input('url');
        $width = (int) $request->input('width');
        $height = (int) $request->input('height');
        echo generateImageThumbfromURL($url, $width, $height);
    }
}
