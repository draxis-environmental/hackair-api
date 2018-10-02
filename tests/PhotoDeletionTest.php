<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class PhotoDeletionTest extends TestCase
{

    use DatabaseTransactions;
    public $connectionsToTransact = ['mongodb'];

    /**
     * Successful photo deletion test.
     *
     * @return void
     */

     public function testPhotoDeletion() {
         // TODO authenticate, call /photos?user_id={user_id} to retrieve photos
         // Response should be json object and have 200 status code
     }
}
