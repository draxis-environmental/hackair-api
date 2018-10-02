<?php
/**
 * Created by PhpStorm.
 * User: Ap
 * Date: 02/11/16
 * Time: 16:30
 */

namespace App\Libraries;
use Jenssegers\Mongodb\Eloquent\Model as MongoModel;
use Validator;
use Illuminate\Support\MessageBag;

class PhotoFlickrValidator extends MongoModel
{
    protected $connection = 'mongodb';
    protected $table = 'photos';

    protected $rules = array(

        'loc.type'          => 'required',
        'loc.coordinates'   => 'required|array',
        'datetime.$date'    => 'required',
        'source_type'       => 'required',

        'source_info.id'         => 'required',
        'source_info.image_name' => 'required',
        'source_info.page_url'   => 'required|url',
        'source_info.image_url'  => 'required|url',
        'source_info.title'      => 'required',
        'source_info.views'      => 'required',
        'source_info.user.username'   => 'required',
        'source_info.query'      => 'required',
        'source_info.date_uploaded.$date' => 'required|numeric',
    );

    protected $errors;

    public function validate($data)
    {
        $v = Validator::make($data, $this->rules);

        if ($v->fails())
        {
            $this->errors = $v->errors()->toArray();
            return false;
        }

        return true;
    }

    public function errors()
    {
        return $this->errors;
    }

}
