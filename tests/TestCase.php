<?php

use App\User;

class TestCase extends Laravel\Lumen\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    public function loginWithFakeUser()
    {
        $user = new User([
            'id' => 1,
            'username' => 'hackair'
        ]);

        $this->be($user);
    }
}
