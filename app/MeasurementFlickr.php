<?php

namespace App;

use App\Measurement;

class MeasurementFlickr extends Measurement {

    // Validation for source-specific info
    protected $rules = array(
        'source_info.id'        => 'required',
        'source_info.query'     => 'required',
        'source_info.text'      => 'required',
        'source_info.views'     => 'required',
        'source_info.username'  => 'required',
        'source_info.pageurl'   => 'required',
        'source_info.imageurl'  => 'required',
    );


}
