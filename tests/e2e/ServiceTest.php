<?php

use App\Models\Service;
use App\Models\User;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Broken;
use App\Models\History;
use Illuminate\Support\Carbon;

class ServiceTest extends TestCase
{

    // create
    public function testShouldCreateService()
    {
        $parameters = [
            'namaCustomer' => 'saitama',
            'noHp' => '085235690084',
            'bisaWA' => false,
            'namaProduk' => 'laptop testing',
            'kategori' => 'notebook',
            'keluhan' => 'lagi ditesting',
            'butuhPersetujuan' => false,
            'kelengkapan' => 'baterai',
            'catatan' => 'password e ora',
            'uangMuka' => '1000',
            'estimasiBiaya' => '2000',
            'cacatProduk' => 'baret'
        ];
        $this->post('/services', $parameters, ['Authorization' => 'Bearer ' . $this->cs()]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'idService'
            ]
        ]);
    }

    // update
    public function testShouldUpdateService()
    {
        $data = Service::orderByDesc('id')->first();
        $parameters = [
            'namaCustomer' => 'goku',
            'noHp' => '085235690023',
            'bisaWA' => true,
            'namaProduk' => 'hp testing',
            'kategori' => 'hp',
            'keluhan' => 'lagi ditest',
            'butuhPersetujuan' => true,
            'kelengkapan' => 'tas',
            'catatan' => 'passwordnya 123',
            'uangMuka' => '4000',
            'estimasiBiaya' => '7000',
            'cacatProduk' => 'mulus'
        ];
        $this->put('/services/' . $data->id, $parameters, ['Authorization' => 'Bearer ' . $this->cs()]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'idService'
            ]
        ]);
    }

    // get all
    public function testShouldReturnAllService()
    {
        $this->get('/services', ['Authorization' => 'Bearer ' . $this->cs()]);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => ['*' => [
                'id',
                'kode',
                'keluhan',
                'status',
                'totalBiaya',
                'diambil',
                'disetujui',
                'klien' => [
                    'nama',
                    'noHp'
                ],
                'produk' => [
                    'nama',
                    'kategori'
                ]
            ]]
        ]);
    }

    // get service queue
    public function testShouldReturnAllServiceQueue()
    {

        $data = User::where('peran', 'teknisi')->first();
        $header = ['Authorization' => 'Bearer ' . $this->teknisi()];
        $this->get('/services/' . $data->username . '/queue', $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => ['*' => [
                'id',
                'kode',
                'keluhan',
                'status',
                'disetujui',
                'produk' => [
                    'nama',
                    'kategori'
                ]
            ]]
        ]);
    }

    // get service progress
    public function testShouldReturnAllServiceProgress()
    {

        $data = User::where('peran', 'teknisi')->first();
        $header = ['Authorization' => 'Bearer ' . $this->teknisi()];
        $this->get('/services/' . $data->username . '/progress', $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => ['*' => [
                'id',
                'kode',
                'keluhan',
                'status',
                'disetujui',
                'produk' => [
                    'nama',
                    'kategori'
                ]
            ]]
        ]);
    }

    // update service status
    public function testShouldUpdateServiceStatus()
    {
        $data = Service::orderByDesc('id')->first();
        $parameters = ['status' => 'mulai diagnosa'];
        $header = ['Authorization' => 'Bearer ' . $this->teknisi()];
        $this->put('/services/' . $data->id . '/status', $parameters, $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'idService'
            ]
        ]);
    }

    // set confirmation cost
    public function testShouldSetConfirmCost()
    {
        $data = Service::orderByDesc('id')->first();
        $header = ['Authorization' => 'Bearer ' . $this->owner()];
        $this->put('/services/' . $data->id . '/confirm-cost', [], $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'idService'
            ]
        ]);
    }

    // update warranty
    public function testShouldUpdateServiceWarranty()
    {
        $data = Service::orderByDesc('id')->first();
        $parameters = ['garansi' => '1 bulan'];
        $header = ['Authorization' => 'Bearer ' . $this->owner()];
        $this->put('/services/' . $data->id . '/warranty', $parameters, $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'idService'
            ]
        ]);
    }

    // set service confirmation
    public function testShouldSetServiceConfirmation()
    {
        $data = Service::orderByDesc('id')->first();
        $parameters = ['disetujui' => true];
        $header = ['Authorization' => 'Bearer ' . $this->owner()];
        $this->put('/services/' . $data->id . '/confirmation', $parameters, $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'idService'
            ]
        ]);
    }

    // set service taking
    public function testShouldSetServiceTake()
    {
        $data = Service::orderByDesc('id')->first();
        $header = ['Authorization' => 'Bearer ' . $this->cs()];
        $this->put('/services/' . $data->id . '/take', [], $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'idService'
            ]
        ]);
    }

    // delete service
    public function testShouldDeleteService()
    {
        $data = Service::orderByDesc('id')->first();
        $header = ['Authorization' => 'Bearer ' . $this->owner()];
        $this->delete('/services/' . $data->id, $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message'
        ]);
    }

    private function create()
    {
        $customer = Customer::create([
            'nama' => 'ujiCoba',
            'noHp' => '6285715463861',
            'bisaWA' => true
        ]);
        $product = Product::create([
            'nama' => 'testing',
            'kategori' => 'hp'
        ]);
        $service = Service::create([
            'kode' => '123456789',
            'keluhan' => 'testing',
            'status' => 'antri',
            'idCustomer' => $customer->id,
            'idProduct' => $product->id,
            'butuhPersetujuan' => true,
            'disetujui' => true,
            'konfirmasiBiaya' => false,
            'diambil' => false,
            'waktuMasuk' => Carbon::now('GMT+7'),
            'usernameCS' => '2206003',
            'usernameTeknisi' => '2206002'
        ]);
        $broken = Broken::create([
            'judul' => 'lagi dicoba',
            'deskripsi' => 'in test mode',
            'idService' => $service->id
        ]);
        $history = History::create([
            'idService' => $service->id,
            'status' => 'antri',
            'pesan' => 'antrinya dicoba',
            'waktu' => Carbon::now('GMT+7')
        ]);
    }

    // get service by id with brokens,customer and product
    public function testShouldreturnServiceWithAllRelation()
    {
        $this->create();
        $data = Service::orderByDesc('id')->first();
        $header = ['Authorization' => 'Bearer ' . $this->cs()];
        $this->get('/services/' . $data->id . '/detail?include=klien,produk,kerusakan', $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'id', 'kode', 'keluhan', 'status', 'totalBiaya', 'totalBiayaString', 'diambil', 'disetujui', 'estimasiBiaya', 'estimasiBiayaString', 'uangMuka', 'uangMukaString', 'yangHarusDibayar', 'tanggalMasuk', 'jamMasuk', 'tanggalAmbil', 'jamAmbil', 'garansi', 'usernameCS', 'usernameTeknisi', 'butuhPersetujuan', 'sudahKonfirmasiBiaya', 'klien' => [
                    'nama',
                    'noHp',
                    'bisaWA'
                ], 'produk' => [
                    'nama',
                    'kategori',
                    'cacatProduk',
                    'kelengkapan',
                    'catatan'
                ], 'kerusakan' => ['*' => [
                    'id',
                    'judul',
                    'biaya',
                    'disetujui'
                ]]
            ]
        ]);
    }

    public function testShouldreturnServiceWithKlien()
    {
        $data = Service::orderByDesc('id')->first();
        $header = ['Authorization' => 'Bearer ' . $this->cs()];
        $this->get('/services/' . $data->id . '/detail?include=klien', $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'id', 'kode', 'keluhan', 'status', 'totalBiaya', 'totalBiayaString', 'diambil', 'disetujui', 'estimasiBiaya', 'estimasiBiayaString', 'uangMuka', 'uangMukaString', 'yangHarusDibayar', 'tanggalMasuk', 'jamMasuk', 'tanggalAmbil', 'jamAmbil', 'garansi', 'usernameCS', 'usernameTeknisi', 'butuhPersetujuan', 'sudahKonfirmasiBiaya', 'klien' => [
                    'nama',
                    'noHp',
                    'bisaWA'
                ]
            ]
        ]);
    }

    public function testShouldreturnServiceWithProduk()
    {
        $data = Service::orderByDesc('id')->first();
        $header = ['Authorization' => 'Bearer ' . $this->cs()];
        $this->get('/services/' . $data->id . '/detail?include=produk', $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'id', 'kode', 'keluhan', 'status', 'totalBiaya', 'totalBiayaString', 'diambil', 'disetujui', 'estimasiBiaya', 'estimasiBiayaString', 'uangMuka', 'uangMukaString', 'yangHarusDibayar', 'tanggalMasuk', 'jamMasuk', 'tanggalAmbil', 'jamAmbil', 'garansi', 'usernameCS', 'usernameTeknisi', 'butuhPersetujuan', 'sudahKonfirmasiBiaya', 'produk' => [
                    'nama',
                    'kategori',
                    'cacatProduk',
                    'kelengkapan',
                    'catatan'
                ]
            ]
        ]);
    }

    public function testShouldreturnServiceWithKerusakan()
    {
        $data = Service::orderByDesc('id')->first();
        $header = ['Authorization' => 'Bearer ' . $this->cs()];
        $this->get('/services/' . $data->id . '/detail?include=kerusakan', $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'id', 'kode', 'keluhan', 'status', 'totalBiaya', 'totalBiayaString', 'diambil', 'disetujui', 'estimasiBiaya', 'estimasiBiayaString', 'uangMuka', 'uangMukaString', 'yangHarusDibayar', 'tanggalMasuk', 'jamMasuk', 'tanggalAmbil', 'jamAmbil', 'garansi', 'usernameCS', 'usernameTeknisi', 'butuhPersetujuan', 'sudahKonfirmasiBiaya', 'kerusakan' => ['*' => [
                    'id',
                    'judul',
                    'biaya',
                    'disetujui'
                ]]
            ]
        ]);
    }

    // get service by id
    public function testShouldReturnService()
    {
        $data = Service::orderByDesc('id')->first();
        $header = ['Authorization' => 'Bearer ' . $this->cs()];
        $this->get('/services/' . $data->id . '/detail', $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'id', 'kode', 'keluhan', 'status', 'totalBiaya', 'totalBiayaString', 'diambil', 'disetujui', 'estimasiBiaya', 'estimasiBiayaString', 'uangMuka', 'uangMukaString', 'yangHarusDibayar', 'tanggalMasuk', 'jamMasuk', 'tanggalAmbil', 'jamAmbil', 'garansi', 'usernameCS', 'usernameTeknisi', 'butuhPersetujuan', 'sudahKonfirmasiBiaya'
            ]
        ]);
    }

    // track
    public function testShouldReturnTrackingInfo()
    {
        $data = Service::orderByDesc('id')->first();
        $this->get('/services/' . $data->kode . '/track');
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message',
            'data' => [
                'kode',
                'status',
                'disetujui',
                'totalBiaya',
                'produk' => [
                    'nama',
                    'kategori'
                ],
                'kerusakan' => ['*' => [
                    'judul',
                    'deskripsi',
                    'biaya',
                    'disetujui'
                ]],
                'riwayat' => ['*' => [
                    'status',
                    'pesan',
                    'tanggal',
                    'jam'
                ]]
            ]
        ]);

        Customer::where('id', $data->idCustomer)->delete();
        Product::where('id', $data->idProduct)->delete();
        Broken::where('idService', $data->id)->delete();
        History::where('idService', $data->id)->delete();
        Service::where('id', $data->id)->delete();
    }
}