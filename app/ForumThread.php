<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumThread extends Model
{
    use SoftDeletes;

    public static $rules = [
        'author_id' => 'required|integer',
        'title'     => 'required|string',
        'body'      => 'required|string',
        'sticky'    => 'boolean',
        'closed'    => 'boolean'
    ];

    protected $fillable = ['author_id', 'title', 'body', 'sticky', 'closed'];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];


    // Relationships

    public function tags()
    {
        return $this->belongsToMany('App\ForumTag')->withTimestamps();;
    }


    public function replies()
    {
        return $this->hasMany('App\ForumReply')->with('author');
    }


    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }
}
