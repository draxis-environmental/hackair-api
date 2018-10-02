<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserFollower extends Model {

    use SoftDeletes;

    protected $table = 'follower_user';

    protected $fillable = ['follower_id'];

    protected $dates = ['updated_at', 'created_at', 'deleted_at'];

    public static $rules = [
        // Validation rules
    ];

    // Relationships

}
