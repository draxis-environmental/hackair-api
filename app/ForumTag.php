<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumTag extends Model
{
    use SoftDeletes;

    public static $rules = [
        'name' => 'required|string|max:10'
    ];

    protected $fillable = ['name'];

    // Validation rules

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];


    // Relationships

    public function threads()
    {
        return $this->belongsToMany('App\ForumThread')->with('author');
    }

}
