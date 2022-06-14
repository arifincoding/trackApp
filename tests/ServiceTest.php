<?php
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Service;

class ServiceTest extends TestCase{

    // create
    public function testShouldCreateService(){
        $parameters = [
            'namaCustomer'=>'saitama',
            'noHp'=>'085235690084',
            'bisaWA'=>false,
            'namaProduk'=>'laptop testing',
            'kategori'=>'notebook',
            'keluhan'=>'lagi ditesting',
            'butuhPersetujuan'=>false,
            'kelengkapan'=>'baterai',
            'catatan'=>'password e ora',
            'uangMuka'=>'1000',
            'estimasiBiaya'=>'2000',
            'cacatProduk'=>'baret'
        ];
        $this->post('/services',$parameters,['Authorization'=>'Bearer '.$this->cs()]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data'=>[
                'idService'
            ]
            ]);
    }

    // update
    public function testShouldUpdateService(){
        $data = Service::orderByDesc('id')->first();
        $parameters = [
            'namaCustomer'=>'goku',
            'noHp'=>'085235690023',
            'bisaWA'=>true,
            'namaProduk'=>'hp testing',
            'kategori'=>'hp',
            'keluhan'=>'lagi ditest',
            'butuhPersetujuan'=>true,
            'kelengkapan'=>'tas',
            'catatan'=>'passwordnya 123',
            'uangMuka'=>'4000',
            'estimasiBiaya'=>'7000',
            'cacatProduk'=>'mulus'
        ];
        $this->put('/services/'.$data->id,$parameters,['Authorization'=>'Bearer '.$this->cs()]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data'=>[
                'idService'
            ]
            ]);
    }

    // get all
    public function testShouldReturnAllService(){
        $this->get('/services',['Authorization'=>'Bearer '.$this->cs()]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data'=>['*'=>[
                'id',
                'kode',
                'keluhan',
                'status',
                'totalBiaya',
                'diambil',
                'disetujui',
                'customer'=>[
                    'nama',
                    'noHp'
                ],
                'product'=>[
                    'nama',
                    'kategori'
                ]
            ]]
                ]);
    }

    // get service by id
    public function testShouldReturnService(){
        $data = Service::orderByDesc('id')->first();
        $header = ['Authorization'=>'Bearer '.$this->cs()];
        $this->get('/services/'.$data->id.'/detail',$header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data'=>[
                'idService'
                ,'idCustomer'
                ,'idProduk'
                ,'kode'
                ,'keluhan'
                ,'status'
                ,'totalBiaya'
                ,'totalBiayaString'
                ,'diambil'
                ,'disetujui'
                ,'estimasiBiaya'
                ,'estimasiBiayaString'
                ,'uangMuka'
                ,'uangMukaString'
                ,'yangHarusDibayar'
                ,'tanggalMasuk'
                ,'jamMasuk'
                ,'tanggalAmbil'
                ,'jamAmbil'
                ,'garansi'
                ,'usernameCS'
                ,'usernameTeknisi'
                ,'butuhPersetujuan'
                ,'sudahKonfirmasiBiaya'
            ]
                ]);
    }

    // get service queue
    public function testShouldReturnAllServiceQueue(){
        $header = ['Authorization'=>'Bearer '.$this->teknisi()];

        $this->get('/services/2206002/queue',$header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data'=>['*'=>[
                'id',
                'kode',
                'keluhan',
                'status',
                'disetujui',
                'product'=>[
                    'nama',
                    'kategori'
                ]
            ]]
            ]);
    }

    // get service progress
    public function testShouldReturnAllServiceProgress(){
        $header = ['Authorization'=>'Bearer '.$this->teknisi()];
        $this->get('/services/2206002/progress',$header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data'=>['*'=>[
                'id',
                'kode',
                'keluhan',
                'status',
                'disetujui',
                'product'=>[
                    'nama',
                    'kategori'
                ]
            ]]
            ]);
    }

    // update service status
    public function testShouldUpdateServiceStatus(){
        $data = Service::orderByDesc('id')->first();
        $parameters = ['status'=>'mulai diagnosa'];
        $header = ['Authorization'=>'Bearer '.$this->teknisi()];
        $this->put('/services/'.$data->id.'/status',$parameters,$header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data'=>[
                'idService'
            ]
            ]);
    }

    // set service taking
    public function testShouldSetServiceTake(){
        $data = Service::orderByDesc('id')->first();
        $header = ['Authorization'=>'Bearer '.$this->cs()];
        $this->put('/services/'.$data->id.'/take',[],$header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data'=>[
                'idService'
            ]
            ]);
    }

    // set confirmation cost
    public function testShouldSetConfirmCost(){
        $data = Service::orderByDesc('id')->first();
        $header = ['Authorization'=>'Bearer '.$this->owner()];
        $this->put('/services/'.$data->id.'/confirm-cost',[],$header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data'=>[
                'idService'
            ]
            ]);
    }

    // update warranty
    public function testShouldUpdateServiceWarranty(){
        $data = Service::orderByDesc('id')->first();
        $parameters = ['garansi'=>'1 bulan'];
        $header = ['Authorization'=>'Bearer '.$this->owner()];
        $this->put('/services/'.$data->id.'/warranty',$parameters,$header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data'=>[
                'idService'
            ]
            ]);
    }

    // set service confirmation
    public function testShouldSetServiceConfirmation(){
        $data = Service::orderByDesc('id')->first();
        $parameters = ['disetujui'=>true];
        $header = ['Authorization'=>'Bearer '.$this->owner()];
        $this->put('/services/'.$data->id.'/confirmation',$parameters,$header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data'=>[
                'idService'
            ]
            ]);
    }

    // delete service
    // public function testShouldDeleteService(){
    //     $data = Service::orderByDesc('id')->first();
    //     $header = ['Authorization'=>'Bearer '.$this->owner()];
    //     $this->delete('/services/'.$data->id,$header);
    //     $this->seeStatusCode(200);
    //     $this->seeJsonStructure([
    //         'status',
    //         'message'
    //         ]);
    // }
}