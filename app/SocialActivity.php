<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialActivity extends Model {

    protected $fillable = ['name'];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public static $rules = [
        // Validation rules
    ];

    // Relationships

    public function user()
    {
        return $this->belongsToMany('App\User', 'user_social_activity');
    }

}
