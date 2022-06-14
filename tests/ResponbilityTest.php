<?php
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\User;
use App\Models\Responbility;
use App\Models\Category;

class ResponbilityTest extends TestCase{

    // create responbility
    public function testShouldCreateResponbility(){
        $user = User::where('peran','teknisi')->orderByDesc('id')->first();
        $category = Category::orderByDesc('id')->first();
        $parameters = [
            'idKategori'=> [$category->id]
        ];
        $header = ['Authorization'=>'Bearer '.$this->owner()];
        $this->post('/employes/'.$user->id.'/technician/responbilities',$parameters,$header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message'
            ]);
    }

    // get all responbility by username
    public function testShouldReturnAllRespobilityByUsername(){
        $data = User::where('peran','teknisi')->orderByDesc('id')->first();
        $header = ['Authorization'=>'Bearer '.$this->owner()];
        $this->get('/employes/'.$data->username.'/technician/responbilities',$header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data'=>['*'=>[
                'id',
                'kategori'=>[
                    'nama'
                ]
            ]]
            ]);
    }

    // delete responbility
    public function testShouldDeleteResponbility(){
        $data = Responbility::orderByDesc('id')->first();
        $header = ['Authorization'=>'Bearer '.$this->owner()];
        $this->delete('/employes/technician/responbilities/'.$data->id,$header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message'
        ]);
    }
}