<?php

namespace App\Libraries;
use Jenssegers\Mongodb\Eloquent\Model as MongoModel;
use Validator;
use Illuminate\Support\MessageBag;

class PhotoSkyValidator extends MongoModel
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

    public function validatePhotoTimestamp($coordinates, $photoTimestamp)
    {
        $lng = $coordinates[0];
        $lat = $coordinates[1];
        $result = SunriseSunset::getTimes($lat, $lng, date('Y-m-d'));

        if ((isset($result) == true) && (isset($result->results) == true)) {
            $sunrise_str = $result->results->sunrise;
            $sunset_str = $result->results->sunset;
            $timestamp_after = strtotime($sunrise_str . ' + 90 minutes');
            $timestamp_before = strtotime($sunset_str . ' - 90 minutes');

            if ($photoTimestamp > $timestamp_after && $photoTimestamp < $timestamp_before) {
                return true;
            }
        }

        return false;
    }

}
