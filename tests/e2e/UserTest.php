<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class UserTest extends TestCase
{

    use DatabaseMigrations;
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
                'namaDepan',
                'namaBelakang',
                'jenisKelamin',
                'noHp',
                'peran',
                'email',
                'alamat'
            ]
        ]);
    }

    // update
    public function testShouldUpdateAccount()
    {
        $params = [
            'email' => 'mark@yahoo.com',
            'noHp' => '085235690084',
            'alamat' => 'pojok kampung'
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
            'sandiLama' => 'rahasia',
            'sandiBaru' => 'GuBjhG6I'
        ];
        $this->put('/user/change-password', $params, ['Authorization' => 'Bearer ' . $this->getToken('customer service')]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message'
        ]);
    }
}