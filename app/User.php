<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Lumen\Auth\Authorizable;
use App\Libraries\ModelValidator;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Achievement;
use App\Photo;

class User extends ModelValidator implements JWTSubject, AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, SoftDeletes;

    /**
     * Static validation rules used for registration
     */
    public static $static_rules = [
            'email'=> 'required|email|unique:users,email',
            'username'=> 'required|min:2|unique:users,username'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'username',
        'email',
        'password',
        'profile_picture',
        'year_of_birth',
        'location',
        'location_str',
        'place_id',
        'place',
        'city',
        'country',
        'gender',
        'outdoor_job',
        'groups',
        'outdoor_activities',
        'unsubscribe_token',
        'notify_email',
        'notify_push',
        'onboarding_complete',
        'private',
        'affiliate_id',
        'referred_by',
        'activities_visible',
        'language'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'created_at', 'updated_at', 'deleted_at', 'level_id', 'unsubscribe_token'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

     /**
     * Database table
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'location' => 'array',
        'place' => 'array'
    ];

    /**
     * Generate instance specific validation rules
     * used for user update
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email|unique:users,email,' . $this->id,
            'username' => 'required|min:2|unique:users,username,' . $this->id
        ];
    }

    /**
     * Get sensors for user
     *
     * @return Sensor
     */
    public function sensors()
    {
        return $this->hasMany('App\Sensor');
    }

    /**
     * Get photos for user
     *
     * @return Photo
     */
    public function photos()
    {
        return Photo::where('source_info.user.id', '=', $this->id)->get();
    }

    /**
     * Get photos count for user
     *
     * @return integer
     */
    public function photosCount()
    {
        return Photo::where('source_info.user.id', '=', $this->id)->count();
    }

    /**
     * Get groups for user
     *
     * @return UserGroup
     */
    public function groups()
    {
        return $this->belongsToMany('App\UserGroup');
    }

    /**
     * Get outdoor activities for user
     *
     * @return OutdoorActivity
     */
    public function outdoorActivities()
    {
        return $this->belongsToMany('App\OutdoorActivity');
    }

    /**
     * Get perceptions for user
     *
     * @return Perceptions
     */
    public function perceptions()
    {
        return $this->hasMany('App\Perception');
    }

    /**
     * Get level for user
     *
     * @return Level
     */
    public function level()
    {
        return $this->belongsTo('App\Level');
    }

    /**
     * Get achievements for user
     *
     * @return Achievement
     */
    public function achievements()
    {
        return $this->belongsToMany('App\Achievement');
    }

    /**
     * Get missions for user
     *
     * @return Mission
     */
    public function missions()
    {
        return $this->belongsToMany('App\Mission');
    }

    public function activations()
    {
        return $this->hasMany('App\UserActivation');
    }

    public function actions()
    {
        return $this->hasMany('App\ActionUser');
    }

    public function threads()
    {
        return $this->hasMany('App\ForumThread');
    }

    public function replies()
    {
        return $this->hasMany('App\ForumReply');
    }

    /**
     * Get the UserFollower models for user
     *
     * @return UserFollower
     */
    public function followers()
    {
        return $this->hasMany('App\UserFollower');
    }

    /**
     * Get the followers users for user
     *
     * @return Collection
     */
    public function followersUsers()
    {
        $users = User::join('follower_user','follower_user.follower_id','=','users.id')
            ->where('follower_user.user_id', $this->id)
            ->whereNull('follower_user.deleted_at')
            ->whereNull('users.deleted_at')
            ->get(['users.*']);

        return $users;
    }

    /**
     * Get who the user follows
     *
     * @return UserFollower
     */
    public function following()
    {
        return $this->hasMany('App\UserFollower','follower_id');
    }

    /**
     * Get the users who are being followed
     *
     * @return Collection
     */
    public function followingUsers()
    {
        $users = User::join('follower_user','follower_user.user_id','=','users.id')
            ->where('follower_user.follower_id', $this->id)
            ->whereNull('follower_user.deleted_at')
            ->whereNull('users.deleted_at')
            ->get(['users.*']);

        return $users;
    }

    /**
     * Get user social activities
     *
     * @return UserSocialActivity
     */
    public function socialActivities()
    {
        return $this->hasMany(UserSocialActivity::class);
    }


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    protected static function generateToken()
    {
        return hash_hmac('sha256', str_random(40), config('app.key'));
    }

    /**
     * Attaches a new follower to the user
     * @param $followerId
     */
    public function addFollower($followerId)
    {
        if ($followship = $this->followerExists($followerId)) {
            $followship->restore();
        } else {
            $this->followers()->create([
                'follower_id' => $followerId
            ]);
        }
    }

    /**
     * Detaches an existing follower from the user
     * @param $followerId
     */
    public function deleteFollower($followerId)
    {
        $followerRelationship = UserFollower::where('user_id', $this->id)
            ->where('follower_id',$followerId)
            ->get();

        $this->followers()->delete($followerRelationship);
    }

    /**
     * Checks if a followship exists (even soft-deleted)
     * @param $followerId
     * @return UserFollower
     */
    protected function followerExists($followerId)
    {
        $relationship = UserFollower::where('user_id', $this->id)
            ->where('follower_id', $followerId)
            ->withTrashed()
            ->first();

        return $relationship;
    }

    /**
     * Checks if a follower exists
     * @param $followerId
     * @return UserFollower
     */
    public function hasFollower($followerId)
    {
        $relationship = UserFollower::where('user_id', $this->id)
            ->where('follower_id', $followerId)
            ->first();

        if($relationship)
            return TRUE;
        else
            return FALSE;

       // return $relationship;
    }

    /**
     * Returns the full name of a user
     * @return string
     */
    public function getFullname()
    {
        if (empty($this->name) && empty($this->surname)) {
            return null;
        } else {
            return $this->name . ' ' . $this->surname;
        }
    }

    /**
     * Checks if the current user is following a specific user
     * @param $userId
     * @return UserFollower
     */
    public function isFollowing($userId)
    {
        $relationship = UserFollower::where('user_id', $userId)
            ->where('follower_id', $this->id)
            ->first();

        return $relationship;
    }

    /**
     * Returns the social communities the user is a member
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function socialCommunities()
    {
        return $this->belongsToMany(SocialActivity::class, 'user_social_community');
    }

    /**
     * Returns the social communities the user owns
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mySocialCommunities()
    {
        return $this->hasMany(SocialActivity::class, 'owner_id');
    }


    public function getCommunities() {

        $communities = \DB::table('user_social_community')
                        ->join('social_communities', 'user_social_community.social_community_id', '=', 'social_communities.id')
                        ->where('user_social_community.user_id',$this->id)->whereNull('deleted_at')
                        ->select('social_communities.*')
                        ->get();

        return $communities;
    }


    /**********************************************
     * Personalized Recommendations helper methods
     **********************************************/

    /**
     * Returns if the user belongs to the pregnancy user group
     * @return bool
     */
    public function isPregnant()
    {
        // get pregnancy group id
        $pregrantGroupId = UserGroup::where('name', 'Pregnancy')->pluck('id');

        // search pivot table
        $relationship =$this->groups()
            ->where('user_group_id', $pregrantGroupId)
            ->first();

        // return boolean
        return !is_null($relationship);
    }

    /**
     * Returns if the user belongs to the outdoor job user outdoor activity
     * @return bool
     */
    public function isOutdoorJobUser()
    {
        // get outdoor job user group id
        $outdoorJobActivityId = OutdoorActivity::where('name', 'Outdoor job')->pluck('id');

        // search pivot table
        $relationship = $this->outdoorActivities()
            ->where('outdoor_activity_id', $outdoorJobActivityId)
            ->first();

        // return boolean
        return !is_null($relationship);
    }

    /**
     * Returns if the user has any health sensitivities (other than pregnancy)
     * @return bool
     */
    public function isSensitiveTo()
    {
        // get pregnancy group id
        $pregrantGroupId = UserGroup::where('name', 'Pregnancy')->pluck('id');

        // search pivot table
        $sensitivities = $this->groups()
            ->where('user_group_id', '!=', $pregrantGroupId)
            ->pluck('name')
            ->toArray();

        // return all sensitivity names in lowercase
        return array_map('strtolower', $sensitivities);
    }

    /**
     * Returns users Achievements (badges)
     * @return array
     */

    public function getAchievements() {

        $achievements = \DB::table('achievement_user')
            ->join('achievements', 'achievement_user.achievement_id', '=', 'achievements.id')
            ->where('achievement_user.user_id',$this->id)
            ->select('achievements.*')
            ->get();

        return $achievements;

    }

    /**
     * Returns the user's outdoor activities
     * @return array
     */
    public function getOutdoorActivities()
    {
        // search pivot table
        $activities = $this->outdoorActivities()->pluck('name')->toArray();

        // return all outdoor activity names in lowercase
        return array_map('strtolower', $activities);
    }
}
