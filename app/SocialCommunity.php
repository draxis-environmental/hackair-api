<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\UserSocialCommunity;


class SocialCommunity extends Model {

    use SoftDeletes;

    protected $fillable = ['owner_id', 'name', 'description', 'profile_picture'];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public static $rules = array(
        'owner_id'      => 'required|integer|min:1',
        'name'          => 'required|min:2|unique:social_communities,name',
        'description'   => 'required|min:5',
    );

    // Relationships

    /**
     * Returns community's members
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'user_social_community')
            ->withTimestamps();
    }

    public function getMembers() {

        $members = \DB::table('user_social_community')
            ->join('users', 'user_social_community.user_id', '=', 'users.id')
            ->where('user_social_community.id',$this->id)
            ->select('users.id','users.name','users.surname','users.username','users.profile_picture')
            ->get();

        return $members;
    }

    public function isMember($userId) {

        $member = UserSocialCommunity::where('user_id',$userId)->where('social_community_id',$this->id)->first();
        return ($member) ? true : false;

    }

    /**
     * Returns the community owner user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class,'owner_id');
    }

}
