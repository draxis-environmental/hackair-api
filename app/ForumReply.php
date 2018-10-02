<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumReply extends Model
{

    use SoftDeletes;

    public static $rules = [
        'thread_id' => 'required|integer',
        'author_id' => 'required|integer',
        'body'      => 'required|string',
    ];

    protected $fillable = ['thread_id', 'author_id', 'body'];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];


    // Relationships

    public function thread()
    {
        return $this->belongsTo('App\ForumThread');
    }


    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }

}
