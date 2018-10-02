<?php

namespace App;

use App\Libraries\ModelValidator;
use Dimsav\Translatable\Translatable;

class Level extends ModelValidator 
{
    use Translatable;

    public $translationModel = 'App\LevelTranslation';
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
    public $translatedAttributes = ['name'];

    /**
     * The attributes that will be hidden when querying model.
     *
     * @var array
     */
    protected $hidden = array('created_at', 'updated_at', 'translations');

     /**
     * Database table
     *
     * @var string
     */
    protected $table = 'levels';
    
    /**
     * Get users for level.
     */
    public function users()
    {
        return $this->hasMany('App\User');
    }
}
