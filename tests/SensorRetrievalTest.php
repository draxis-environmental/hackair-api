<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class SensorRetrievalTest extends TestCase
{

    use DatabaseTransactions;
    public $connectionsToTransact = ['mongodb'];

    /**
     * Successful sensors retrieval test.
     *
     * @return void
     */

     public function testUserSensorsRetrieval() {
         // TODO authenticate, call /sensors?user_id={user_id} to retrieve sensors
         // Response should be json object and have 200 status code
     }

     /**
      * Unauthorized request to retrieve user sensors test.
      *
      * @return void
      */

      public function testUserSensorsRetrievalUnauthorized() {
          // TODO call /sensors?user_id={user_id} without authenticating
          // Response should have 401 status code
      }

      /**
       * Successful sensors retrieval test.
       *
       * @return void
       */

       public function testSensorRetrieval() {
           // TODO authenticate, call /sensors/{id} to retrieve sensor
           // Response should be json object and have 200 status code
       }
}
