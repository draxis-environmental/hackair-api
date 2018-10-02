<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as MongoModel;

class Photo extends MongoModel {

    protected $connection = 'mongodb';
    protected $table = 'photos';

    public function transform($properties){

        foreach ($properties as $key => $value) {
            if ($key == 'loc') {
                $value = json_decode($value);
            }

            $this->$key = $value;
        }
    }

}
