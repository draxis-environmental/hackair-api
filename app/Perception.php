<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use App\Libraries\ModelValidator;

class Perception extends ModelValidator
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'perception',
        'location'
    ];

    /**
     * Database table
     *
     * @var string
     */
    protected $table = 'perceptions';

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
