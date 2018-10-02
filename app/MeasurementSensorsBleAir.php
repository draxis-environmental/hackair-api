<?php

namespace App;

use App\Measurement;

class MeasurementSensorsBleAir extends Measurement {

    // Validation for source-specific info
    protected $rules = array(
        'source_info.user.id'        => 'required',
        'source_info.user.username'  => 'required',
//        'source_info.sensor.id'      => 'required'
    );

}
