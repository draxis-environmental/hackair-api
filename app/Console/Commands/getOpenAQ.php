<?php
/**
 * Created by PhpStorm.
 * User: jimi
 * Date: 27/9/2017
 * Time: 1:45 μμ
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;

use App\Measurement;
use MongoDB\BSON\UTCDateTime as MongoDate;



class getOpenAQ extends Command
{
    protected $name = 'get-openaq';

    protected $description = 'Import measurements from CERTH crawler module';

    public function fire()
    {

        $measurements = [];


        echo "Getting measurements...\n";
        $measurements = array_merge($measurements, $this->getData('pm25','openaq'));
        $measurements = array_merge($measurements, $this->getData('pm10','openaq'));
        $measurements = array_merge($measurements, $this->getData('pm25','luftdaten'));
        $measurements = array_merge($measurements, $this->getData('pm10','luftdaten'));

        foreach ($measurements as $key => $measurement) {
            try {
                $data = $this->transform($measurement);
                $this->saveMeasurement($data);
            }
            catch (\Exception $e) {

                continue;

            }
        }

        echo "Finished\n";
    }

    protected function getOptions()
    {
        return array();
    }

    protected function getData($pollutant = '', $source = '')
    {
        echo "Requesting ".$pollutant." measurements from ".$source." ...\n";

        $client = new Client();
        $url = "https://services.hackair.eu:8083/crawl/sensors?pollutant=".$pollutant."&index=perfect,good,medium,bad&recent=true&source=".$source;
        $res = $client->request('GET', $url,['verify' => false]);
        $result = json_decode($res->getBody());

        echo "Found ".$result->num_results." measurements ...\n";


        return $result->measurements;
    }

    protected function transform($data)
    {
        if ($data->pollutant == "pm25") {
            $pollutant = "PM2.5_AirPollutantValue";
        } else {
            $pollutant = "PM10_AirPollutantValue";
        }

        $properties = [
            'pollutant_q'  => [
                'name'  => $pollutant,
                'value' => $data->value
            ],
            'loc'               => $data->loc,
            'datetime'          => new MongoDate($data->datetime),
            'date_str'          => $data->datetime,
            'source_type'       => 'webservices',
            'source_info'       => [
                'countryCode' => $data->countryCode,
                'location'    => $data->location,
                'source'      => $data->source_type
            ]
        ];


        return $properties;


    }

    protected function saveMeasurement($data)
    {
        $measurement = new Measurement;
        $measurement->transform($data);
        $measurement->validate($data);
        $measurement->save();
    }


}