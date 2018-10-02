<?php

// use Laravel\Lumen\Testing\DatabaseTransactions;

// class MeasurementSearchTest extends TestCase
// {
//     use DatabaseTransactions;
//     public $connectionsToTransact = ['mongodb'];

//     /**
//     * Test failure when no timestampStart parameter.
//     *
//     * @return void
//     */

//     public function testFailureTimeStampStart() {
//         $this->get('/measurements')
//              ->seeJson([
//                 'timestampStart' => ['The timestamp start field is required.']
//              ]);
//     }

//     /**
//     * Test measurement retrieval with only timestampStart parameter.
//     *
//     * @return void
//     */

//     public function testGetMeasurementsTimeStampStart() {
//         $response = $this->call('GET','/measurements?timestampStart=1');

//         $this->assertEquals(200, $response->status());
//     }

//     /**
//     * Test measurement retrieval within given date range.
//     *
//     * @return void
//     */

//     public function testGetMeasurementsDateRange() {

//     }

//     /**
//     * Test measurement retrieval within geoJSON coordinates.
//     *
//     * @return void
//     */

//     public function testGetMeasurementsLocation() {
//         $response = $this->call('GET','/measurements?timestampStart=1&location=-20,30|45,60');

//         $this->assertEquals(200, $response->status());
//     }

//     /**
//     * Test measurement retrieval based on source.
//     *
//     * @return void
//     */

//     public function testGetMeasurementsSource() {

//     }

// }
