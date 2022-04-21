<?php

class AuthTest extends TestCase {

    // login and get token
    public function testShouldReturnLoginToken(){
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

    // get refresh token
    public function testShouldRefreshToken(){
        $header = ['Authorization'=>'Bearer '.$this->owner()];
        $this->post('/user/refresh',[],$header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'token',
            'token_type',
            'expires_in'
        ]);
    }

    // logout and delete token
    public function testShouldLogout(){
        $header = ['Authorization'=>'Bearer '.$this->owner()];
        $this->post('/user/logout',[],$header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message'
        ]);
    }
}