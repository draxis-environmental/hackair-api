<?php namespace App;

use App\Libraries\ModelValidator;

class AdditionalProfile extends ModelValidator {

    protected $dates = ['created_at', 'updated_at'];
    protected $table = 'additional_profiles';
    protected $fillable = ['user_id', 'firstname','lastname','gender','user_groups','user_activities','year_of_birth'];


}
