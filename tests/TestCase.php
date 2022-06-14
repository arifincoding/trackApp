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
        return $this->setToken('2206001','pzH5Rjro');
    }
    protected function cs(){
        return $this->setToken('2206003','GuBjhG6I');
    }
    protected function teknisi(){
        return $this->setToken('2206002','xQpdjyZF');
    }
}