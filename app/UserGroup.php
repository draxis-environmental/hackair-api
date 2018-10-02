<?php

namespace App;

use App\Libraries\ModelValidator;


class UserGroup extends ModelValidator
{
    protected $rules = array(
        'name' => 'required|min:2'
    );

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
    protected $hidden = array('created_at', 'updated_at', 'pivot');

     /**
     * Database table
     *
     * @var string
     */
    protected $table = 'user_groups';
}
