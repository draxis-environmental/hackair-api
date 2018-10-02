<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;

class UserSocialCommunity extends Model {

    protected $dates = ['created_at', 'updated_at'];

    protected $table = 'user_social_community';


}
