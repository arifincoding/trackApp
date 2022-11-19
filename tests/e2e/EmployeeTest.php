<?php

use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;

class EmployeeTest extends TestCase
{

    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        User::factory()->count(2)->create(['peran' => 'teknisi']);
    }
    // create employee
    public function testShouldCreateEmployee()
    {
        $parameters = [
            'namaDepan' => 'pikachu',
            'namaBelakang' => 'testing',
            'jenisKelamin' => 'pria',
            'noHp' => '088678987655',
            'alamat' => 'jl coba kota testing',
            'peran' => 'teknisi',
            'email' => 'pikachu@yahoo.com'
        ];
        $header = ['Authorization' => 'Bearer ' . $this->getToken('pemilik')];

        $this->post('/employes', $parameters, $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'idPegawai'
            ]
        ]);
    }

    // get all employee
    public function testShouldReturnAllEmployee()
    {
        $header = ['Authorization' => 'Bearer ' . $this->getToken('pemilik')];
        $this->get('/employes', $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => ['*' => [
                'idPegawai',
                'username',
                'nama',
                'noHp',
                'peran'
            ]]
        ]);
    }

    // update employee
    public function testShouldUpdateEmployee()
    {
        $parameters = [
            'namaDepan' => 'saitama',
            'namaBelakang' => 'coba testing',
            'jenisKelamin' => 'wanita',
            'noHp' => '088678987656',
            'alamat' => 'jl test kota testing',
            'peran' => 'teknisi',
            'email' => 'saitama@gmail.com'
        ];
        $header = ['Authorization' => 'Bearer ' . $this->getToken('pemilik')];

        $this->put('/employes/1', $parameters, $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'idPegawai'
            ]
        ]);
    }

    // get employee by id
    public function testShouldReturnEmployee()
    {
        $header = ['Authorization' => 'Bearer ' . $this->getToken('pemilik')];
        $this->get('/employes/1', $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'idPegawai',
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

    // delete employee
    public function testShouldDeleteEmployee()
    {
        $header = ['Authorization' => 'Bearer ' . $this->getToken('pemilik')];
        $this->delete('/employes/1', $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message'
        ]);
    }
}