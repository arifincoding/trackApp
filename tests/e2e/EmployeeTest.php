<?php

use App\Models\User;

class EmployeeTest extends TestCase{

    // create employee
    public function testShouldCreateEmployee(){
        $parameters = [
            'namaDepan'=>'pikachu',
            'namaBelakang'=>'testing',
            'jenisKelamin'=>'pria',
            'noHp'=>'088678987655',
            'alamat'=>'jl coba kota testing',
            'peran'=>'teknisi',
            'email'=>'pikachu@yahoo.com'
        ];
        $header = ['Authorization'=>'Bearer '.$this->owner()];

        $this->post('/employes',$parameters,$header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data'=>[
                'idPegawai'
            ]
            ]);
    }

    // get all employee
    public function testShouldReturnAllEmployee(){
        $header = ['Authorization'=>'Bearer '.$this->owner()];
        $this->get('/employes',$header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data'=>['*'=>[
                'idPegawai',
                'username',
                'nama',
                'noHp',
                'peran'
            ]]
            ]);
    }

    // update employee
    public function testShouldUpdateEmployee(){
        $data = User::orderByDesc('id')->first();
        $parameters = [
            'namaDepan'=>'saitama',
            'namaBelakang'=>'coba testing',
            'jenisKelamin'=>'wanita',
            'noHp'=>'088678987656',
            'alamat'=>'jl test kota testing',
            'peran'=>'teknisi',
            'email'=>'saitama@gmail.com'
        ];
        $header = ['Authorization'=>'Bearer '.$this->owner()];

        $this->put('/employes/'.$data->id,$parameters,$header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data'=>[
                'idPegawai'
            ]
            ]);
    }

    // get employee by id
    public function testShouldReturnEmployee(){
        $data = User::orderByDesc('id')->first();
        $header = ['Authorization'=>'Bearer '.$this->owner()];
        $this->get('/employes/'.$data->id,$header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data'=>[
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
    public function testShouldDeleteEmployee(){
        $data = User::orderByDesc('id')->first();
        $header = ['Authorization'=>'Bearer '.$this->owner()];
        $this->delete('/employes/'.$data->id,$header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message'
        ]);
    }
}