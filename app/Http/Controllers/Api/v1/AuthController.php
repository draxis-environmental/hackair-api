<?php
/**
 * Created by PhpStorm.
 * User: Ap
 * Date: 01/11/16
 * Time: 22:31
 */

namespace App\Http\Controllers\Api\v1;

use App\Events\FollowerAdded;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Contracts\Providers\Auth;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\Hash;
use App\Libraries\Responder;
use App\User;
use App\UserActivation;
use \Illuminate\Support\Facades\Lang;
use Event;
use App\Events\UserRegistered;
use App\Events\EmailConfirmed;


class AuthController extends Controller
{
    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    public function login(Request $request)
    {
        $payload = app('request')->only('email', 'password');
        $rules = [
            'email'    => 'required|email|max:255',
            'password' => 'required'
        ];
        $validator = app('validator')->make($payload, $rules);

        if ($validator->fails()) {
            return Responder::ValidationError( Lang::get('responses.invalidInput')  );
        } else {
            try {

                $credentials = [
                    'email' => $request->input('email'),
                    'password' => $request->input('password'),
                    'active' => 1,
                    'deleted_at' => null
                ];

                // TODO: separate message in case of inactive, non-existent, wrong credentials cases
                if (! $token = $this->jwt->attempt($credentials)) {
                    $params = [ 'message' => Lang::get('responses.unknown_credentials') ];
                    return Responder::NotFoundError($params);
                }
                else {
                    if(\Auth::user()->activated == false) {
                        $params = [ 'message' => Lang::get('responses.unconfirmed') ];
                        return Responder::NotFoundError($params);


                    }
                }

            } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

                return Responder::ServerError('Token has expired.');

            } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

                return Responder::ServerError('Token is invalid.');

            } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

                return Responder::ServerError(['Token is absent.', $e->getMessage()]);

            }

            return Responder::SuccessLoginResponse( $token );
        }
    }

    public function refreshToken(Request $request)
    {
        $token = $this->jwt->getToken();
        if (!$token) {

            return Responder::ServerError('Token not provided.');
        }
        try {
            $token = $this->jwt->refresh();
        } catch(TokenInvalidException $e){
            return Responder::ServerError('Token is invalid.');
        }
        return Responder::SuccessResponse( $token );
    }

    public function register(Request $request)
    {
        $payload = $request->all();

        // use static validation rules from model
        $validator = app('validator')->make($payload, User::$static_rules);

        if ($validator->fails()) {

            $errors = $validator->errors();

            if ($errors->has('email')) {

                $user = User::withTrashed()->where('email',$request->input('email'))->first();

                if($user && $user->deleted_at !== null )
                    return Responder::ValidationError( Lang::get('auth.deactivated_account') );


                $errorMessage = $errors->first('email');
            }
            else if ($errors->has('username')) {
                $errorMessage = $errors->first('username');
            }
            else
                $errorMessage = Lang::get('responses.unknown_credentials');


            return Responder::ValidationError( $errorMessage );
        } else {

            $user = new User;
            $user->name = $request->input('name');
            $user->surname = $request->input('surname');
            $user->username = $request->input('username');
            $user->email    = $request->input('email');
            $user->password = Hash::make($request->input('password'));
            $user->affiliate_id = str_random(10);
            $user->active = 1;
            $user->private = true;

            if($request->has('permission')) {
                if($request->input('permission') == true)
                    $user->bund_permission = 1;
                else
                    $user->bund_permission = 0;
            }

            if($request->has('accept_newsletters')) {
                if($request->input('accept_newsletters') == true)
                    $user->accept_newsletters = 1;
                else
                    $user->accept_newsletters = 0;
            }

            $user->save();

            $userActivation = new UserActivation();
            $userActivation->user_id  = $user->id;
            $userActivation->token = UserActivation::generateToken();
            $userActivation->save();

            Event::fire(new UserRegistered($user, $userActivation->token));

            // if the registration form points out a referrer
            if ($request->has('referrer')) {
                $refId = $request->input('referrer');
                $referrer = User::where('affiliate_id', $refId)->first();
                // if the referrer exists
                if ($referrer) {
                    // mark the new user as referred
                    $user->referred_by = $referrer->affiliate_id;
                    $user->save();
                }
            }


            $confirmMessage =  Lang::get('auth.confirm');
            return Responder::SuccessCreateResponse( $user ,$confirmMessage);
        }
    }

    public function confirmEmail($confirmation_token)
    {
        if (!$confirmation_token) {
            return redirect(env('WEB_URL'));
        }

        $userActivation = UserActivation::where('token',$confirmation_token)->first();

        if (!$userActivation) {
            return redirect(env('WEB_URL'));
        }

        $user = User::findOrFail($userActivation->user_id);
        $user->activated = 1;
        $user->notify_email = true;
        $user->notify_push = true;
        $user->unsubscribe_token = User::generateToken();
        $user->save();

        $userActivation->delete();

        Event::fire(new EmailConfirmed($user));

        // if this user was referred by someone
        if ($user->referred_by) {
            $referrer = User::where('affiliate_id', $user->referred_by)->first();

            // if referrer still exists
            if ($referrer) {

                // add them both as followers
                $user->addFollower($referrer->id);
                $referrer->addFollower($user->id);

                // send email to the referrer about his friend now following him
                Event::fire(new FollowerAdded($referrer->id, $user->id));

                // TODO: credit referrer for gamification
            }

        }

        return redirect(env('WEB_URL'));
    }
}
