<?php

namespace App;

use App\Libraries\ModelValidator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Dimsav\Translatable\Translatable;

/**
 * Class Mission
 * 
 * Base class that may be extended for more specific missions.
 */

class Mission extends ModelValidator 
{
    use Translatable;

    public $translationModel = 'App\MissionTranslation';
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
    public $translatedAttributes = ['name', 'description', 'description_short', 'motivation', 'cta_text', 'feedback_success'];

    /**
     * The attributes that will be hidden when querying model.
     *
     * @var array
     */
    protected $hidden = array('created_at', 'updated_at', 'missionable_id', 'missionable_type', 'translations');

     /**
     * Database table
     *
     * @var string
     */
    protected $table = 'missions';

    /**
     * Get action that the mission requires.
     */
    public function action()
    {
        return $this->belongsTo('App\Action');
    }

    /**
     * Get achievement that the mission relates to.
     */
    public function achievement()
    {
        return $this->belongsTo('App\Achievement');
    }

    /**
     * Get restrictions related to the mission.
     */
    public function restrictions()
    {
        return $this->hasMany('App\Restriction');
    }
    

    /**
     * Get new mission.
     *
     * @return App\Mission $mission
     */
    public static function getNew()
    {
        $now = Carbon::now();
        $mission = Mission::where(function ($query) use ($now) {
                $query->whereNull('datetime_starts')->orWhere('datetime_starts', '<=', $now);
            })->where(function ($query) use ($now) {
                $query->whereNull('datetime_ends')->orWhere('datetime_ends', '>=', $now);
            })->orderBy('datetime_starts', 'desc')
            ->first();

        return $mission;
    }

    /**
     * Get available missions.
     *
     * @return array $missions
     */
    public static function getAvailable()
    {
        $missions = [];
        $user = Auth::user();
        $now = Carbon::now();

        $available_missions = Mission::where(function ($query) use ($now) {
                $query->whereNull('datetime_starts')->orWhere('datetime_starts', '<=', $now);
            })->where(function ($query) use ($now) {
                $query->whereNull('datetime_ends')->orWhere('datetime_ends', '>=', $now);
            })->orderBy('datetime_starts', 'desc')
            ->get();

        $completed_mission_ids = $user->missions()->pluck('mission_id')->toArray();
        
        foreach($available_missions as $row) {
            if (in_array($row->id, $completed_mission_ids) == false) {
                $missions[] = $row;
            }
        }
    
        return $missions;
    }

    /**
     * Get completed missions.
     *
     * @return array $missions
     */
    public static function getCompleted()
    {
        $user = Auth::user();
        $missions = $user->missions()->get();

        return $missions;
    }
}
