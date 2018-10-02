<?php

namespace App;

use App\Measurement;

class MeasurementSensorsArduino extends Measurement {

    // Validation for source-specific info
    protected $rules = array(
        'source_info.user.id'        => 'required',
        'source_info.user.username'  => 'required',
        'source_info.sensor.id'      => 'required',
        'source_info.sensor.battery' => 'required',
        'source_info.sensor.tamper'  => 'required',
        'source_info.sensor.error'   => 'required'
    );

}
