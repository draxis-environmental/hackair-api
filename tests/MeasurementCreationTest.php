<?php

// use Laravel\Lumen\Testing\DatabaseTransactions;

// class MeasurementCreationTest extends TestCase
// {

//     use DatabaseTransactions;
//     public $connectionsToTransact = ['mongodb'];

//     /**
//      * Valid source_type test.
//      *
//      * @return void
//      */

//      public function testSourceType() {
//          $this->post('/measurements', [
//              'source_type' => ''
//          ])
//          ->seeJson([
//              'code' => 422,
//              'status' => 'error',
//              'type' => 'input',
//              'message' => 'Invalid source type attribute'
//          ]);
//      }

//     /**
//      * Flickr source validation test.
//      *
//      * @return void
//      */
//     public function testFlickrCreation() {

//         $response = $this->call('POST', '/measurements', [
//             'pollutant_q' => [
//                 'name' => 'PM10_AirPollutantValue',
//                 'value' => '100',
//                 'unit' => 'mg'
//             ],
//             'pollutant_i' => [
//                 'name' => 'PM10_AirPollutantIndex',
//                 'index' => 'Perfect'
//             ],
//             'city'        => 'Thessaloniki',
//             'loc'         => [
//                 'type' => 'Point',
//                 'coordinates' => [
//                     13.361433,
//                     52.497913
//                 ]
//             ],
//             'datetime'    => '2016-10-10T12:00:00Z',
//             'source_type' => 'flickr',
//             'source_info' => [
//                 'id'        => '123',
//                 'query'     => 'Thessaloniki',
//                 'text'      => 'Abeautifulskyimage',
//                 'views'     => '10',
//                 'username'  => 'lefman',
//                 'pageurl'   => 'http://a.url.com',
//                 'imageurl'  => 'http://a.url.jpg'
//             ]
//         ]);
//         $this->assertEquals(200, $response->status());
//     }

//     /**
//      * Sensor Arduino source validation test.
//      *
//      * @return void
//      */
//     public function testSensorsArduinoCreation() {

//         $response = $this->call('POST', '/measurements', [
//             'pollutant_q' => [
//                 'name' => 'PM10_AirPollutantValue',
//                 'value' => '100',
//                 'unit' => 'mg'
//             ],
//             'pollutant_i' => [
//                 'name' => 'PM10_AirPollutantIndex',
//                 'index' => 'Perfect'
//             ],
//             'city'        => 'Thessaloniki',
//             'loc'         => [
//                 'type' => 'Point',
//                 'coordinates' => [
//                     13.361433,
//                     52.497913
//                 ]
//             ],
//             'datetime'    => '2016-10-10T12:00:00Z',
//             'source_type' => 'sensors_arduino',
//             'source_info' => [
//                 'user'    => [
//                     'id'        => 15,
//                     'username'  => 'johnD'
//                 ],
//                 'sensor'  => [
//                     'id'        => 10,
//                     'battery'   => 60,
//                     'tamper'    => 0,
//                     'error'     => 0
//                 ]
//             ]
//         ]);
//         $this->assertEquals(200, $response->status());
//     }

//     /**
//      * Sensor BleAir source validation test.
//      *
//      * @return void
//      */
//     public function testSensorsBleairCreation() {

//         $response = $this->call('POST', '/measurements', [
//             'pollutant_q' => [
//                 'name' => 'PM10_AirPollutantValue',
//                 'value' => '100',
//                 'unit' => 'mg'
//             ],
//             'pollutant_i' => [
//                 'name' => 'PM10_AirPollutantIndex',
//                 'index' => 'Perfect'
//             ],
//             'city'        => 'Thessaloniki',
//             'loc'         => [
//                 'type' => 'Point',
//                 'coordinates' => [
//                     13.361433,
//                     52.497913
//                 ]
//             ],
//             'datetime'    => '2016-12-10T12:00:00Z',
//             'source_type' => 'sensors_bleair',
//             'source_info' => [
//                 'user'    => [
//                     'id'        => 15,
//                     'username'  => 'johnD'
//                 ],
//                 'sensor'  => [
//                     'id'        => 10
//                 ]
//             ]
//         ]);
//         $this->assertEquals(200, $response->status());
//     }

//     /**
//      * Sensor BleAir source validation test.
//      *
//      * @return void
//      */
//     public function testSensorsCOTSCreation() {

//         $response = $this->call('POST', '/measurements', [
//             'pollutant_q' => [
//                 'name' => 'PM10_AirPollutantValue',
//                 'value' => '100',
//                 'unit' => 'mg'
//             ],
//             'pollutant_i' => [
//                 'name' => 'PM10_AirPollutantIndex',
//                 'index' => 'Perfect'
//             ],
//             'city'        => 'Thessaloniki',
//             'loc'         => [
//                 'type' => 'Point',
//                 'coordinates' => [
//                     13.361433,
//                     52.497913
//                 ]
//             ],
//             'datetime'    => '2016-01-10T12:00:00Z',
//             'source_type' => 'sensors_cots',
//             'source_info' => [
//                 'user'    => [
//                     'id'        => 15,
//                     'username'  => 'johnD'
//                 ],
//                 'sensor'  => [
//                     'id'        => 10
//                 ]
//             ]
//         ]);
//         $this->assertEquals(200, $response->status());
//     }
// }
