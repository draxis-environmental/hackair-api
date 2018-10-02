<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class PhotoRetrievalTest extends TestCase
{

    use DatabaseTransactions;
    public $connectionsToTransact = ['mongodb'];

    /**
     * Successful photo retrieval test.
     *
     * @return void
     */

     public function testUserPhotosRetrieval() {
         // TODO authenticate, call /photos?user_id={user_id} to retrieve photos
         // Response should be json object and have 200 status code
     }

     /**
      * Unauthorized request to retrieve user photos test.
      *
      * @return void
      */

      public function testUserPhotosRetrievalUnauthorized() {
          // TODO call /photos?user_id={user_id} without authenticating
          // Response should have 401 status code
      }
}
