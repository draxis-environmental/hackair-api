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

class MeasurementValidator extends MongoModel
{
    protected $connection = 'mongodb';
    protected $table = 'measurements';

    protected $base_rules = array(
        'pollutant_q.name'  => 'required',
        'pollutant_q.value' => 'required',
        'pollutant_q.unit'  => 'required',

        'pollutant_i.name'  => 'required',
        'pollutant_i.index' => 'required',

        'city'              => 'required',
        'loc'               => 'required',
        'datetime'          => 'required',
        'source_type'       => 'required'
    );
    protected $rules = array();

    protected $errors;

    public function validate($data)
    {
        $merged_rules = array_merge($this->base_rules, $this->rules);
        $v = Validator::make($data, $merged_rules);

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
