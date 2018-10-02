<?php
/**
 * Created by PhpStorm.
 * User: Ap
 * Date: 02/11/16
 * Time: 16:30
 */

namespace App\Libraries;
use Illuminate\Database\Eloquent\Model;
use Validator;
use Illuminate\Support\MessageBag;

class ModelValidator extends Model
{
    protected $rules = array();

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