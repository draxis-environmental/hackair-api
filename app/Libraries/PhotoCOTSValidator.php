<?php

namespace App\Libraries;
use Jenssegers\Mongodb\Eloquent\Model as MongoModel;
use Validator;
use Illuminate\Support\MessageBag;

class PhotoCOTSValidator extends MongoModel
{
    protected $connection = 'mongodb';
    protected $table = 'photos';

    protected $rules = array(

        // 'loc.type'          => 'required',
        // 'loc.coordinates'   => 'required|array', TODO: Fixme
        'datetime'          => 'required',
        'source_type'       => 'required',

        'source_info.image_url'   => 'required',
        'source_info.user.id'     => 'required',
        'source_info.user.username' => 'required',
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
