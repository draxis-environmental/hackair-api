<?php
/**
 * Created by PhpStorm.
 * User: Ap
 * Date: 02/11/16
 * Time: 16:30
 */

namespace App\Libraries;


class AirQualityIndex
{
    public static function getAQI($lon, $lat, $dateStart, $dateEnd = null)
    {
        ini_set ('max_execution_time', 1600 );
        ini_set('memory_limit','712M');

        $dateEnd = empty($dateEnd) == false ? $dateEnd : date('Y-m-d');

        $aqi = [];

        $query = "SELECT ST_Value(rast, ST_GeomFromText('POINT(" . $lon . " " . $lat . ")',4326)) as val, created_at
                  FROM fused_data
                  WHERE created_at::date BETWEEN '"  . $dateStart . "' AND '"  . $dateEnd . "'
                  AND ST_Value(rast, ST_GeomFromText('POINT(" . $lon . " " . $lat . ")',4326)) IS NOT NULL;";


        $db_result = app('db')->select($query);


        foreach($db_result as $row) {
            $aqi_q = [
                'name' => 'AQI_Value',
                'value' => $row->val
            ];
            $aqi_i = self::toIndex($aqi_q);

            $aqi[] = [
                'AQI_Value' => (float) $aqi_q['value'],
                'AQI_Index' => $aqi_i['index'],
                'date' => date('Y-m-d', strtotime($row->created_at))
            ];
        }

        return $aqi;
    }

    static function toIndex($pollutant_q) {
        $scales = array(
           'perfect' => ['AOD_AirPollutantValue'   => ['min' => 0,   'max' => 0.14],
                         'PM10_AirPollutantValue'  => ['min' => 0,   'max' => 20],
                         'PM2.5_AirPollutantValue' => ['min' => 0,   'max' => 10],
                         'PM_AirPollutantValue'    => ['min' => 0,   'max' => 0],
                         'AQI_Value'               => ['min' => 0.5, 'max' => 1.5]
                         ],
           'good'    => ['AOD_AirPollutantValue'   => ['min' => 0.14,'max' => 0.34],
                         'PM10_AirPollutantValue'  => ['min' => 20,  'max' => 50],
                         'PM2.5_AirPollutantValue' => ['min' => 10,  'max' => 25],
                         'PM_AirPollutantValue'    => ['min' => 0,   'max' => 0],
                         'AQI_Value'               => ['min' => 1.5, 'max' => 2.5]
                         ],
           'medium'  => ['AOD_AirPollutantValue'   => ['min' => 0.34,'max' => 0.44],
                         'PM10_AirPollutantValue'  => ['min' => 50,  'max' => 70],
                         'PM2.5_AirPollutantValue' => ['min' => 25,  'max' => 35],
                         'PM_AirPollutantValue'    => ['min' => 0,   'max' => 0],
                         'AQI_Value'               => ['min' => 2.5, 'max' => 3.5]
                         ],
           'bad'     => ['AOD_AirPollutantValue'   => ['min' => 0.44],
                         'PM10_AirPollutantValue'  => ['min' => 70],
                         'PM2.5_AirPollutantValue' => ['min' => 35],
                         'PM_AirPollutantValue'    => ['min' => 0],
                         'AQI_Value'               => ['min' => 3.5, 'max' => 4.5]
                         ]
       );

       $metric = $pollutant_q['name'];
       $value = $pollutant_q['value'];
       $pollutant_i = [
           "name" =>  str_replace('Value', 'Index', $metric),
           "index" => ""
       ];

        if ($value == $scales['perfect'][$metric]['min'] || ($value > $scales['perfect'][$metric]['min'] && $value <= $scales['perfect'][$metric]['max'])) {
            $pollutant_i['index'] = 'perfect';
        } else if ($value > $scales['good'][$metric]['min'] && $value <= $scales['good'][$metric]['max']) {
            $pollutant_i['index'] = 'good';
        } else if ($value > $scales['medium'][$metric]['min'] && $value <= $scales['medium'][$metric]['max']) {
            $pollutant_i['index'] = 'medium';
        } else if ($value > $scales['bad'][$metric]['min']) {
            $pollutant_i['index'] = 'bad';
        }

        return $pollutant_i;
    }


}
