<?php

namespace App;

use DB;
use App\Libraries\ModelValidator;
use Carbon\Carbon;

/**
 * Class UserActivation
 * @package App
 */
class UserActivation extends ModelValidator
{
    protected $fillable = ['user_id', 'token'];

    protected $rules = array(
        'user_id'=> 'integer',
        'token'=> 'required|unique:user_activations'
    );

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    protected static function generateToken()
    {
        return hash_hmac('sha256', str_random(40), config('app.key'));
    }

    private function regenerateToken()
    {
        $token = $this->generateToken();
        UserActivation::where('user_id', $this->user_id)->update([
            'token' => $token,
            'created_at' => Carbon::now()
        ]);
        return $token;
    }

    private function createToken()
    {
        $token = $this->generateToken();
        UserActivation::insert([
            'user_id' => $this->user_id,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);
        return $token;
    }

    public function createActivation()
    {
        $activation = $this->getActivation();

        if (!$activation) {
            return $this->createToken();
        }
        return $this->regenerateToken();

    }

    public function getActivation()
    {
        return UserActivation::where('user_id', $this->user_id)->first();
    }

    public function getActivationByToken($token)
    {
        return UserActivation::where('token', $token)->first();
    }
}
