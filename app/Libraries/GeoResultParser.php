<?php
/**
 * Created by PhpStorm.
 * User: maria
 * Date: 4/9/2017
 * Time: 12:22 PM
 */

namespace App\Libraries;


class GeoResultParser
{
    public static function parse($result, $options = array())
    {
        $results = [];
        foreach ($result->address_components as $address) {
            $addressType = $address->types[0];
            if (in_array($addressType, $options)) {
                $results[$addressType] = $address;
            }
        }

        return $results;
    }
}