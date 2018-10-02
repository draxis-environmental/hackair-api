<?php

namespace App;

use App\Libraries\PhotoFlickrValidator;

class PhotoFlickr extends PhotoFlickrValidator {

    public function transform($properties){

        foreach ($properties as $key => $value) {
            $this->$key = $value;
        }
    }

}
