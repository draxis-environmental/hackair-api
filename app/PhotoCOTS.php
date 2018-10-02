<?php

namespace App;

use App\Libraries\PhotoCOTSValidator;

class PhotoCOTS extends PhotoCOTSValidator {

    public function transform($properties){

        foreach ($properties as $key => $value) {            
            $this->$key = $value;
        }
    }

}
