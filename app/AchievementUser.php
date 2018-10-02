<?php

namespace App;

use App\Libraries\ModelValidator;

class AchievementUser extends ModelValidator 
{
    protected $rules = array();

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * The attributes that will be hidden when querying model.
     *
     * @var array
     */
    protected $hidden = array('created_at', 'updated_at');

     /**
     * Database table
     *
     * @var string
     */
    protected $table = 'achievement_user';
}
