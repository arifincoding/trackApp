<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class UserTest extends TestCase
{

    use DatabaseTransactions;
    // get by id
    public function testShouldReturnAccount()
    {
        $this->get('/user/account', ['Authorization' => 'Bearer ' . $this->getToken('customer service')]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'username',
                'firstname',
                'lastname',
                'gender',
                'telp',
                'role',
                'email',
                'address'
            ]
        ]);
    }

    // update
    public function testShouldUpdateAccount()
    {
        $params = [
            'email' => 'mark@yahoo.com',
            'telp' => 6285235690084,
            'address' => 'pojok kampung'
        ];
        $this->put('/user/account', $params, ['Authorization' => 'Bearer ' . $this->getToken('customer service')]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message'
        ]);
    }

    // update password
    public function testShouldChangePassword()
    {
        $params = [
            'old_password' => 'rahasia',
            'new_password' => 'GuBjhG6I'
        ];
        $this->put('/user/change-password', $params, ['Authorization' => 'Bearer ' . $this->getToken('customer service')]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message'
        ]);
    }
}
