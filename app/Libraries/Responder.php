<?php
/**
 * Created by PhpStorm.
 * User: Ap
 * Date: 02/11/16
 * Time: 21:04
 */

namespace App\Libraries;

use Log;
use Closure;
use \Illuminate\Support\Facades\Lang;

class Responder
{


    public static function SuccessResponse($data, $message=null, $extra=array())
    {
        $response = [
            'code' => 200,
            'status' => 'success',
            'data' => $data,
            'message'=>$message,
            'count' => sizeof($data)
        ];
        if (empty($extra) == false) {
            $response = array_merge($response, $extra);
        }
        self::handle($response);
        return response()->json($response, $response['code']);
    }

    public static function SuccessCreateResponse($data, $message=null)
    {
        $response = [
            'code' => 201,
            'status' => 'success',
            'count' => sizeof($data),
            'resources' => $data,
            'message' => $message
        ];
        self::handle($response);
        return response()->json($response, $response['code']);
    }


    public static function SuccessLoginResponse($token)
    {
        $response = [
            'code' => 200,
            'status' => 'success',
            'token' => $token
        ];
        self::handle($response);
        return response()->json($response, $response['code']);
    }

    public static function geoJsonResponse($data){
        $response = $data;
        self::handle($response);
        return response()->json($response, 200);
    }

    public static function ValidationError($data)
    {
        $response = [
            'code' => 400,
            'status' => 'error',
            'type'=>'validation',
            'message' => $data
        ];
        self::handle($response);
        return response()->json($response, $response['code']);
    }

    public static function ConflictError($data)
    {
        $response = [
            'code' => 409,
            'status' => 'error',
            'type'=>'conflict',
            'message' => $data
        ];
        self::handle($response);
        return response()->json($response, $response['code']);
    }

    public static function NotFoundError($params = array())
    {
        $message = empty($params['message']) == false ? $params['message'] : Lang::get('responses.resource_not_found');
        $response = [
            'code' => 404,
            'status' => 'error',
            'type'=> 'not_found',
            'message' => $message,
        ];
        self::handle($response);
        return response()->json($response, $response['code']);
    }

    public static function ClientInputError($data)
    {
        $response = [
            'code' => 422,
            'status' => 'error',
            'type' => 'input',
            'message' => $data
        ];
        self::handle($response);
        return response()->json($response, $response['code']);
    }

    public static function ServerError($data)
    {
        $response = [
            'code' => 500,
            'status' => 'error',
            'type' => 'server_error',
            'message' => $data
        ];
        self::handle($response);
        return response()->json($response, $response['code']);
    }

    public static function UnauthorizedError($params = array())
    {
        $message = empty($params['message']) == false ? $params['message'] : Lang::get('responses.unauthorized');
        $response = [
            'code' => 401,
            'status' => 'error',
            'message' => $message
        ];
        self::handle($response);
        return response()->json($response, $response['code']);
    }

    public static function BadRequestError($params = array())
    {
        $message = empty($params['message']) == false ? $params['message'] : Lang::get('responses.bad_request');
        $response = [
            'code' => 400,
            'status' => 'error',
            'type'=> 'bad_request',
            'message' => $message
        ];
        self::handle($response);
        return response()->json($response, $response['code']);
    }

    /**
     * Handle response.
     *
     * @param  \Illuminate\Http\Response  $response
     * @param  \Closure  $next
     * @return mixed
     */
    public static function handle($response)
    {
        if (array_key_exists('code', $response) && $response['code'] == 201) {
            unset($response['resources']);
        }
        //Log::info("Response logged\n" . print_r($response, true) . "\n----\n\n\n");
    }

}
