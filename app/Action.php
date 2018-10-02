<?php

namespace App;

use App\Libraries\ModelValidator;

class Action extends ModelValidator 
{
    protected $rules = array();

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

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
    protected $table = 'actions';

    /**
     * The missions that this model relates to.
     *
     * @return void
     */
    public function missions()
    {
        return $this->hasMany('App\Mission');
    }

    /**
     * The achievements that this model relates to
     *
     * @return void
     */
    public function achievements()
    {
        return $this->hasMany('App\Achievement');
    }
}
