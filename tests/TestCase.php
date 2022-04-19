<?php

use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
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

    private function setToken($username,$password){
        $credentials = ['username'=>$username,'password'=>$password];
        $token = auth()->attempt($credentials);
        return $token;
    }

    protected function owner(){
        return $this->setToken('2204003','62EEFAsw');
    }
    protected function cs(){
        return $this->setToken('2204004','SM5S5yfY');
    }
    protected function teknisi(){
        return $this->setToken('2204006','RUQQyCvm');
    }
}