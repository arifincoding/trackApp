<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Service;
use App\Models\Broken;

class BrokenTest extends TestCase{

    // create broken by id service
    public function testShouldCreateBroken(){
        $data = Service::orderByDesc('id')->first();
        $parameters = [
            'judul'=>'ganti testing',
            'deskripsi'=>'ini adalah testing'
        ];
        $header = ['Authorization'=>'Bearer '.$this->teknisi()];
        $this->post('/services/'.$data->id.'/brokens',$parameters,$header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data'=>[
                'idKerusakan'
            ]
            ]);
    }

    // get all brokens by id service
    public function testShouldReturnAllBroken(){
        $data = Service::orderByDesc('id')->first();
        $header = ['Authorization'=>'Bearer '.$this->teknisi()];
        $this->get('/services/'.$data->id.'/brokens',$header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data'=>['*'=>[
                'id',
                'judul',
                'biaya',
                'disetujui'
            ]]
            ]);
    }

    // update broken by id
    public function testShouldUpdateBroken(){
        $data = Broken::orderByDesc('id')->first();
        $parameters = [
            'judul'=>'ganti coba',
            'deskripsi'=>'ini adalah coba testing'
        ];
        $header = ['Authorization'=>'Bearer '.$this->teknisi()];
        $this->put('/services/brokens/'.$data->id,$parameters,$header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data'=>[
                'idKerusakan',
                'idService'
            ]
            ]);
    }

    // get brokens by id
    public function testShouldReturnBroken(){
        $data = Broken::orderByDesc('id')->first();
        $header = ['Authorization'=>'Bearer '.$this->teknisi()];
        $this->get('/services/brokens/'.$data->id,$header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data'=>[
                'idKerusakan',
                'idService',
                'judul',
                'deskripsi',
                'biaya',
                'disetujui'
            ]
            ]);
    }

    // update broken cost
    public function testShouldUpdateBrokenCost(){
        $data = Broken::orderByDesc('id')->first();
        $parameters = [
            'biaya'=>'3000'
        ];
        $header = ['Authorization'=>'Bearer '.$this->owner()];
        $this->put('/services/brokens/'.$data->id.'/cost',$parameters,$header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data'=>[
                'idKerusakan'
            ]
        ]);
    }

    // update broken confirmation
    public function testShouldUpdateBrokenConfirmation(){
        $data = Broken::orderByDesc('id')->first();
        $parameters = [
            'disetujui'=>true,
        ];
        $header = ['Authorization'=>'Bearer '.$this->owner()];
        $this->put('/services/brokens/'.$data->id.'/confirm',$parameters,$header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data'=>[
                'idKerusakan'
            ]
            ]);
    }

    // delete broken
    public function testShouldDeleteBroken(){
        $data = Broken::orderByDesc('id')->first();
        $header = ['Authorization'=>'Bearer '.$this->teknisi()];
        $this->delete('/services/brokens/'.$data->id,$header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message'
        ]);
    }
}