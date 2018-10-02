<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Auth;


class UserSocialActivity extends Model {

    protected $fillable = ['user_id', 'social_activity_id', 'counter', 'visible', 'object_metadata'];

    protected $dates = ['created_at', 'updated_at'];

    protected $table = 'user_social_activity';

    // Relationships
    /**
     * Get the social activity this user activity record is about
     */
    public function activity()
    {
        return $this->belongsToMany(SocialActivity::class);
    }

    /**
     * Get the user who belongs this user social activity
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Fetch a social activity feed for a set of user ids
     * @param array $followingUserIds
     * @param int $resultsPerPage
     * @return Collection
     */
    public static function getUsersSocialActivityFeed($followingUserIds, $resultsPerPage = 10) {

        if(sizeof($followingUserIds) == 1 && $followingUserIds[0] == Auth::id())
            $personal = array(0,1);
        else
            $personal = array(0);

        $activities = self::join('social_activities', 'social_activities.id', 'user_social_activity.social_activity_id')
            ->join('users', 'users.id', 'user_social_activity.user_id')
            ->whereIn('user_social_activity.user_id', $followingUserIds)
            ->whereIn('users.private', $personal)
            ->where('users.activities_visible', 1)
            ->where('user_social_activity.visible', 1)
            ->select([
                'user_social_activity.id as user_social_activity_id',
                'social_activities.name as social_activity_type',
                'users.id as user_id',
                'users.name',
                'users.surname',
                'users.username',
                'users.profile_picture',
                'user_social_activity.updated_at',
                'user_social_activity.counter',
                'user_social_activity.object_metadata'
            ])
            ->orderBy('user_social_activity.updated_at', 'DESC')
            ->simplePaginate($resultsPerPage);

        // JSON transformations & literals
        $activities->each(function ($item) {
            if ($item->counter > 1) {
                $item->action = Lang::get('social.' . $item->social_activity_type . '_plural', ['count' => $item->counter]);
            } else {
                $item->action = Lang::get('social.' . $item->social_activity_type);
            }

            $item->object_metadata = json_decode($item->object_metadata);
        });

        return $activities;
    }

}
