<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use App\Libraries\ModelValidator;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Sensor extends ModelValidator implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

     protected $rules = array(
        'name' => 'required|min:2'
    );

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'type', 'user_id', 'location', 'access_key', 'mac_address','floor','location_type'
    ];

    /**
     * Database table
     *
     * @var string
     */
    protected $table = 'sensors';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'location' => 'array'
    ];

    /**
     * Get user the sensor belongs to
     *
     * @var string
     */
    public function user()
    {
        return $this->belongsTo('\App\User');
    }
}
