<?php

use App\Models\User;
use Laravel\Lumen\Testing\DatabaseTransactions;

class EmployeeTest extends TestCase
{

    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
    }
    // create employee
    public function testShouldCreateEmployee()
    {
        $parameters = [
            'firstname' => 'pikachu',
            'lastname' => 'testing',
            'gender' => 'pria',
            'telp' => 6288678987655,
            'address' => 'jl coba kota testing',
            'role' => 'teknisi',
            'email' => 'pikachu@yahoo.com'
        ];
        $header = ['Authorization' => 'Bearer ' . $this->getToken('pemilik')];

        $this->post('/employes', $parameters, $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'user_id'
            ]
        ]);
    }

    // get all employee
    public function testShouldReturnAllEmployee()
    {
        User::factory()->count(2)->sequence(
            ['role' => 'teknisi'],
            ['role' => 'customer service']
        )->create();
        $header = ['Authorization' => 'Bearer ' . $this->getToken('pemilik')];
        $this->get('/employes', $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => ['*' => [
                'id',
                'username',
                'name',
                'telp',
                'role'
            ]]
        ]);
    }

    // update employee
    public function testShouldUpdateEmployee()
    {
        $user = User::factory()->cs()->create();
        $parameters = [
            'firstname' => 'saitama',
            'lastname' => 'coba testing',
            'gender' => 'wanita',
            'telp' => 6288678987656,
            'address' => 'jl test kota testing',
            'role' => 'teknisi',
            'email' => 'saitama@gmail.com'
        ];
        $header = ['Authorization' => 'Bearer ' . $this->getToken('pemilik')];

        $this->put("/employes/$user->id", $parameters, $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'user_id'
            ]
        ]);
    }

    // get employee by id
    public function testShouldReturnEmployee()
    {
        $user = User::factory()->tecnician()->create();
        $header = ['Authorization' => 'Bearer ' . $this->getToken('pemilik')];
        $this->get("/employes/$user->id", $header);
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

    // delete employee
    public function testShouldDeleteEmployee()
    {
        $user = User::factory()->create();
        $header = ['Authorization' => 'Bearer ' . $this->getToken('pemilik')];
        $this->delete("/employes/$user->id", $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message'
        ]);
    }
}
