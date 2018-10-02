<?php

namespace App\Libraries;

use GuzzleHttp\Client;

class SunriseSunset
{
    public static function getTimes($lat = '', $lng = '', $date = '')
    {
        $date = empty($date) == false ? $date : date('Y-m-d');

        $client = new Client();
        $url = "https://api.sunrise-sunset.org/json?lat={$lat}&lng={$lng}&date={$date}";
        $res = $client->request('GET', $url);
        $result = json_decode($res->getBody());

        return $result;
    }
}