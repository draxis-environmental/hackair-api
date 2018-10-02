<?php

use Laravel\Lumen\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;

class UserAuthTest extends TestCase
{
    use DatabaseTransactions;

    public $connectionsToTransact = ['pgsql'];

    /**
    * Test successful user registration.
    *
    * @return void
    */

    public function testRegister() {

        $response = $this->call('POST', '/users', [
            'email' => 'hackair123@hackair.eu',
            'name' => 'demo',
            'surname' => 'hackair',
            'username' => 'hackair123',
            'password' => 'hackair'
        ]);

        $this->assertEquals(201, $response->status());
    }

    /**
    * Test successful user login.
    *
    * @return void
    */

    public function testLogin() {

        $user = factory(App\User::class)->create([
             'email' => 'hackair123@hackair.eu',
             'name' => 'demo',
             'surname' => 'hackair',
             'username' => 'hackair123',
             'password' => Hash::make('hackair')
        ]);

        $response = $this->call('POST', '/users/login', [
            'email' => 'hackair123@hackair.eu',
            'password' => 'hackair'
        ]);

        $this->assertEquals(200, $response->status());
    }
}
