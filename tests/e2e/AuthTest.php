<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Testing\DatabaseTransactions;

class AuthTest extends TestCase
{
    use DatabaseTransactions;
    // login and get token
    public function testShouldReturnLoginToken()
    {
        User::factory()->create(['username' => '2211001', 'password' => Hash::make('rahasia')]);
        $parameters = ['username' => '2211001', 'password' => 'rahasia'];
        $this->post('/user/login', $parameters);
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
    public function testShouldRefreshToken()
    {
        $header = ['Authorization' => 'Bearer ' . $this->getToken('pemilik')];
        $this->post('/user/refresh', [], $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'token',
            'token_type',
            'expires_in'
        ]);
    }

    // logout and delete token
    public function testShouldLogout()
    {
        $header = ['Authorization' => 'Bearer ' . $this->getToken('pemilik')];
        $this->post('/user/logout', [], $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message'
        ]);
    }
}
