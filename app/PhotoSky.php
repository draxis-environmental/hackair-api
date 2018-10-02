<?php

namespace App;

use App\Libraries\PhotoSkyValidator;

class PhotoSky extends PhotoSkyValidator {

    public function transform($properties){

        foreach ($properties as $key => $value) {            
            $this->$key = $value;
        }
    }

}
