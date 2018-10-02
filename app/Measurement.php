<?php

namespace App;

use App\Libraries\MeasurementValidator;
use App\Libraries\AirQualityIndex;
use MongoDB\Driver\Cursor;
use MongoDB\BSON\UTCDateTime as MongoDate;
use App\Libraries\MongoHelper;
use Illuminate\Database\Eloquent\Collection;
use Auth;


class Measurement extends MeasurementValidator {

    public static function findAll($properties) {

        $timestampStart = $properties['timestampStart'];

        if($timestampStart == '1970-01-01T00:00:00.000Z') {  //FIXED ASAP
            $day_before = date( 'Y-m-d', strtotime( date('Y-m-d') . ' -30 day' ) );
            $timestampStart = $day_before.'T00:00:00.000Z';

        }

        if (array_key_exists('timestampEnd', $properties)) {
            $timestampEnd = $properties['timestampEnd'];
        } else {
            $timestampEnd = gmdate("Y-m-d\TH:i:s\Z");
        }

        // Base filter
        $filters = [];

        // Location filter
        if (array_key_exists( 'location', $properties )) {
            // e.g. &location=-20,30|45,60
            $coordinates = explode('|', $properties['location']);
            $location = coordinates_str_to_array($coordinates);
            $geometry = count($coordinates) > 2 ? '$polygon' : '$box';
            $filters['loc'] = [
                '$geoWithin' => [
                    $geometry => $location
                ]
            ];
        }

        // Source filter
        if (array_key_exists( 'source', $properties )) {
            // TODO should put this in a Validator
            if (strstr($properties['source'], ',')) {
                $source_types = explode(',', $properties['source']);
                $sourceTypes = $source_types;
            } else {
                $sourceTypes = [$properties['source']];
            }
        } else {
            $sourceTypes = self::sourceTypes();
        }

        // User filter
        if (array_key_exists('user', $properties) && Auth::id() == intval($properties['user'])) {
            $userId = intval($properties['user']);
            $filters['source_info.user.id'] = $userId;
        }

        // Sensor filter, where id;
        if (array_key_exists( 'sensor', $properties )) {
            $sensorId = intval($properties['sensor']);
            $sensor = Sensor::find($sensorId);
            $sourceTypes = ['sensors_' . $sensor['type']];
            if ($sensor && $sensor->user_id == Auth::id()) {
                $filters['source_info.sensor.id'] = $sensorId;
            } else {
                $filters['source_info.sensor.id'] = NULL;
            }
        }

        // Pollutant filter
        if (array_key_exists( 'pollutant', $properties )) {
            // TODO should put this in a Validator
            $pollutants = [
                'pm10' => 'PM10_AirPollutantValue',
                'pm2.5'=> 'PM2.5_AirPollutantValue',
                'pm1'  => 'PM1_AirPollutantValue',
                'pm'   => 'PM_AirPollutantValue',
                'aod'  => 'AOD_AirPollutantValue'
            ];

            $pollutant = $pollutants[ $properties['pollutant'] ];

            $filters['pollutant_q.name'] = $pollutant;
        }

        $filters['datetime'] = [
            '$gt' => new MongoDate(new \DateTime($timestampStart)),
            '$lt' => new MongoDate(new \DateTime($timestampEnd))
        ];


        $fields = ['datetime', 'loc', 'source_type', 'pollutant_q', 'pollutant_i', 'date_str', 'created_at', 'source_info'];

        $show = array_key_exists('show', $properties) ? $properties['show'] : 'latest'; // all, latest, hourly_averages

        // Get measurements for each source type
        $result = new Collection();

        foreach($sourceTypes as $sourceType) {
            switch($sourceType) {
                case 'sensors_arduino':
                case 'sensors_bleair':
                    if ($show == 'hourly_averages') {
                        $groupFields = [
                            '_id' => [
                                'sensor_id' => '$source_info.sensor.id',
                                'pollutant_q_name' => '$pollutant_q.name',
                                'day' => ['$dayOfYear' => '$datetime'],
                                'hour' => ['$hour' => '$datetime']
                            ],
                            'avg_pollutant_q_value' => ['$avg' => '$pollutant_q.value'],
                            'x_id' => ['$last' => '$_id']
                        ];
                    } else if ($show == 'latest') {
                        $groupFields = [
                            '_id' => [
                                'sensor_id' => '$source_info.sensor.id',
                                'pollutant_q_name' => '$pollutant_q.name'
                            ],
                            'x_id' => ['$last' => '$_id']
                        ];
                    } else {
                        $groupFields = [];
                    }
                    break;
                case 'webcams':
                    if ($show == 'hourly_averages') {
                        $groupFields = [
                            '_id' => '$source_info.webcam_id',
                            'avg_pollutant_q_value' => ['$avg' => '$pollutant_q.value'],
                            'x_id' => ['$last' => '$_id']
                        ];
                    } else if ($show == 'latest') {
                        $groupFields = [
                            '_id' => '$source_info.webcam_id',
                            'x_id' => ['$last' => '$_id']
                        ];
                    } else {
                        $groupFields = [];
                    }
                    break;
                case 'webservices':
                    if ($show == 'hourly_averages') {
                        $groupFields = [
                            '_id' => '$_id',
                            'avg_pollutant_q_value' => ['$avg' => '$pollutant_q.value'],
                            'x_id' => ['$last' => '$_id']
                        ];
                    } else if ($show == 'latest') {
                        $groupFields = [
                            '_id' => '$source_info.location',
                            'x_id' => ['$last' => '$_id']
                        ];
                    } else {
                        $groupFields = [];
                    }
                    break;
                default:
                    if ($show == 'hourly_averages') {
                        $groupFields = [
                            '_id' => '$_id',
                            'avg_pollutant_q_value' => ['$avg' => '$pollutant_q.value'],
                            'x_id' => ['$last' => '$_id']
                        ];
                    } else if ($show == 'latest') {
                        $groupFields = [
                            '_id' => '$_id',
                            'x_id' => ['$last' => '$_id']
                        ];
                    } else {
                        $groupFields = [];
                    }
            }

            $filters['source_type'] = $sourceType;

            $projectFields = [
                '_id' => '$x_id'
            ];
            foreach($fields as $f) {
                $projectFields[$f] = 1;
                if ($show != 'all') {
                    $groupFields[$f] = [
                        '$last' => '$' . $f
                    ];
                }
            }
            $projectFields['avg_pollutant_q_value'] = 1;

            $sortFields = ['datetime' => -1];
            $skip = array_key_exists( 'start', $properties ) && $properties['start'] > 0 ? (int) $properties['start'] : 0;
            $limit = array_key_exists( 'length', $properties ) && $properties['length'] > 0  ? (int) $properties['length'] : 600;

            $collection = self::getRawMeasurements($filters, $groupFields, $projectFields, $sortFields, $skip, $limit);
            $result = $result->merge($collection);
        }

        foreach($result as &$row) {
            $row['datetime'] =  MongoHelper::getUnixTimestampFromMongoUTCDateTime($row['datetime']);
            $row['pollutant_q']['unit'] = 'μg/m3';
            //$row['real_datetime'] = $row['created_at'];

            if(isset($row['pollutant_i']['index']) && $row['pollutant_i']['index'] == 'perfect')
                $row['pollutant_i']['index'] = 'very_good';

            if(isset($row['source_info']['user']['username']) && isset($row['source_info']['sensor']['id'])) {

                if($row['source_info']['user']['id'] == Auth::id()) {
                    $row['source_info']['user']['username'] = $row['source_info']['user']['username'].' sID : '.$row['source_info']['sensor']['id'];
                }
                else
                    $row['source_info']['user']['username'] = 'sID :'.$row['source_info']['sensor']['id'];

            }
            
            if ($row['source_type'] == 'webcams') {
                $row['source_info']['url_original'] = empty($row['source_info']['webcam_url']) == false ? $row['source_info']['webcam_url'] : (empty($row['source_info']['url_original']) == false ? $row['source_info']['url_original'] : $row['source_info']['image_url']);
            }
            
            $row['date_str'] = date( 'Y-m-d\TH:i:s\Z', $row['datetime'] );
           // if($row['source_type'] == 'webservices' || $row['source_type'] == 'flickr' || $row['source_type'] == 'webcams') {  /// THIS IS A HACK I NEED TO REPLACE IT UNTIL MONDAY 23 MAR 2018
                $row['real_datetime'] = date( 'Y-m-d H:i:s', $row['datetime'] );
            //}

            if (empty($row['avg_pollutant_q_value']) == false) {
                // Set average as pollutant_q.value
                $row['pollutant_q']['value'] = $row['avg_pollutant_q_value'];
                $row['pollutant_i'] = AirQualityIndex::toIndex($row['pollutant_q']);

                $row['unit'] = 'μg/m3';
                unset($row['avg_pollutant_q_value']);
            }
        }

        // type=geojson?
        if (array_key_exists( 'type', $properties ) && $properties['type'] === 'geojson') {
            return self::serialiseGeoJson($result);
        } else { // type=json (default)
            return $result;
        }
    }

    public static function getRawMeasurements($matchFields, $groupFields, $projectFields, $sortFields, $skip, $limit = 2000) {
        return Measurement::raw(function($collection) use ($matchFields, $groupFields, $projectFields, $sortFields, $skip, $limit) {
            $aggregateFields = [
                ['$match' => $matchFields],
                ['$sort' => $sortFields]
            ];

            if (count($groupFields) > 0) {
                $aggregateFields = array_merge($aggregateFields, [
                    ['$group' => $groupFields],
                    ['$project' => $projectFields],
                    ['$sort' => $sortFields]
                ]);
            }

            if (empty($skip) == false && $skip > 0) {
                $aggregateFields = array_merge($aggregateFields, [
                    ['$skip' => $skip]
                ]);
            }

            if (empty($limit) == false) {
                $aggregateFields = array_merge($aggregateFields, [
                    ['$limit' => $limit]
                ]);
            }


            return $collection->aggregate($aggregateFields);
        });
    }

    public static function sourceTypes() {
        return ['flickr', 'webcams', 'mobile', 'sensors_arduino', 'sensors_bleair', 'sensors_cots', 'websites', 'webservices'];
    }

    public function transform($properties){

        // Create Air quality index from pollutant_q
        $properties['pollutant_i'] = AirQualityIndex::toIndex($properties['pollutant_q']);

        foreach ($properties as $key => $value) {
            $this->$key = $value;
        }
    }

    static public function serialiseGeoJson($data){
        $featureCollection = [
            'type'     => 'FeatureCollection',
            'features' => []
        ];

        foreach ($data as $key => $item) {
            $feature = [
                'type' => 'Feature',
                'geometry' => $item['loc'],
                'properties' => [
                    'id'   => $item['id']
                ]
            ];

            $pollutant = str_replace('_AirPollutantValue', '', $item['pollutant_q']['name']);

            $feature['properties'][$pollutant.'_Value'] = $item['pollutant_q']['value'];
            if (empty($item['pollutant_q']) == false && array_key_exists('unit', $item['pollutant_q'])) {
                $feature['properties'][$pollutant.'_Unit'] =  $item['pollutant_q']['unit'];
            }
            $feature['properties'][$pollutant.'_Index'] = $item['pollutant_i']['index'];
            $feature['properties']['source_type'] = $item['source_type'];
            $feature['properties']['datetime'] = $item['datetime'];
            $feature['properties']['date_str'] = $item['date_str'];

            array_push($featureCollection['features'], $feature);
        }

        return $featureCollection;
    }
}
