<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\User;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->get('/');

        $this->assertEquals(
            $this->app->version(), $this->response->getContent()
        );
    }

    public function testLogin(){
        $parameters = ['username'=>'2204003', 'password'=>'62EEFAsw'];
        $response = $this->post('/user/login',$parameters);
        $this->seeStatusCode(200);
        $this->seeJsonStructure(
            [
                'token',
                'token_type',
                'expires_in'
            ]
        );
    }

    // public function testGetCategory(){

    // }
}