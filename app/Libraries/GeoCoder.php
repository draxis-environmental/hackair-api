<?php

namespace App\Libraries;

use \Http\Adapter\Guzzle6\Client;
use \Geocoder\Provider\GoogleMaps;

class GeoCoder
{
    protected $geocoder;

    function __construct() {
        $adapter  = new \Http\Adapter\Guzzle6\Client();
        $lang = config('geocoder.lang');
        $region = config('geocoder.region');
        $key = config('geocoder.api_key');

        $this->geocoder = new \Geocoder\Provider\GoogleMaps($adapter, $lang, $region, $key);
    }

    public function geocode($address)
    {
        return $this->geocoder->geocode($address);
    }

    public function reverse($latitude, $longitude)
    {
        return $this->geocoder->reverse($latitude, $longitude);
    }

    public function getCity($results) {
        foreach($results as $key => $result) {
            if ($key == 0) {
                return $result->getLocality();
            }
        }
    }
}
