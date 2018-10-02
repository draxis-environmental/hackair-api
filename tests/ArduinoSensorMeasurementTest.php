<?php

// use Laravel\Lumen\Testing\DatabaseTransactions;
// use App\Sensor;


// class ArduinoSensorMeasurementTest extends TestCase
// {
//     use DatabaseTransactions;

//     public $connectionsToTransact = ['mongodb'];

//     public function testSendDataEverything() {
//         $testData = [
//             'timestamp' => '2016-11-12, 01:23:45',
//             'reading'   => [
//                 'PM2.5_AirPollutantValue' => '10',
//                 'PM10_AirPollutantValue'  => '14'
//             ],
//             'battery'   => '60',
//             'tamper'    => '0',
//             'error'     => '0'
//         ];

//         $sensor = Sensor::where('name', 'Draxis Test Sensor')->first();
//         $this->post('/sensors/arduino/measurements', $testData, ['HTTP_Authorization' => $sensor->access_key])
//         ->seeJson([
//             'code' => 201,
//             'count'=> 2
//         ]);
//     }

//     public function testSendDataMinimal(){
//         $testData = [
//             'reading' => [
//                 'PM10_AirPollutantValue' => '10'
//             ]
//         ];
//         $sensor = Sensor::where('name', 'Draxis Test Sensor')->first();
//         $this->post('/sensors/arduino/measurements', $testData, ['HTTP_Authorization' => $sensor->access_key])
//         ->seeJson([
//             'code' => 201,
//             'count' => 1
//         ]);

//     }

//     public function testSendDataTamperDualReading(){
//         $testData = [
//             'reading' => [
//                 'PM2.5_AirPollutantValue' => '10',
//                 'PM10_AirPollutantValue'  => '12'
//             ],
//             'tamper' => '1'
//         ];

//         $sensor = Sensor::where('name', 'Draxis Test Sensor')->first();
//         $this->post('/sensors/arduino/measurements', $testData, ['HTTP_Authorization' => $sensor->access_key])
//         ->seeJson([
//             'code' => 201,
//             'count' => 2
//         ]);
//     }


//     public function testCreateMeasurementsWithDefaultValues(){
//         $testData = [
//             'reading' => [
//                 'PM10_AirPollutantValue' => '10',
//                 'PM2.5_AirPollutantValue' => '11'
//             ]
//         ];

//         $sensor = Sensor::where('name', 'Draxis Test Sensor')->first();
//         $this->post('/sensors/arduino/measurements', $testData, ['HTTP_Authorization' => $sensor->access_key])
//         ->seeJson([
//             'pollutant_q' => [
//                 'name' => 'PM10_AirPollutantValue',
//                 'value' => 10,
//                 'unit' => 'mg'
//             ]
//         ]);
//     }
// }
