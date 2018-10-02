<?php

namespace App;

use App\Libraries\ModelValidator;
use Illuminate\Support\Facades\Auth;
use App\AchievementUser;
use Dimsav\Translatable\Translatable;

class Achievement extends ModelValidator 
{
    use Translatable;

    public $translationModel = 'App\AchievementTranslation';
    public $useTranslationFallback = true;

    protected $rules = array(
        'name' => 'required|min:5'
    );

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * Translated attributes.
     *
     * @var array
     */
    public $translatedAttributes = ['name', 'description', 'motivation'];

    /**
     * The attributes that will be hidden when querying model.
     *
     * @var array
     */
    protected $hidden = array('created_at', 'updated_at', 'pivot', 'translations');

     /**
     * Database table
     *
     * @var string
     */
    protected $table = 'achievements';

    /**
     * Get action that the achievement requires.
     */
    public function action()
    {
        return $this->belongsTo('App\Action');
    }

    /**
     * Get restrictions related to the achievement.
     */
    public function restrictions()
    {
        return $this->hasMany('App\Restriction');
    }
    
    /**
     * Get unlocked achievements.
     *
     * @return array $achievements
     */
    public static function getAvailable()
    {
        $user = Auth::user();
        $achievements = [];

        $all_achievements = Achievement::all();
        $achievements_won_ids = AchievementUser::where('user_id', $user->id)->pluck('achievement_id')->toArray();
        foreach($all_achievements as $row) {
            if (in_array($row->id, $achievements_won_ids) == false) {
                $achievements[] = $row;
            }
        }

        return $achievements;
    }
}
