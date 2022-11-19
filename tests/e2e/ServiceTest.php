<?php

use App\Models\Service;
use App\Models\User;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Broken;
use App\Models\Category;
use App\Models\History;
use App\Models\Responbility;
use Illuminate\Support\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ServiceTest extends TestCase
{

    use DatabaseMigrations;

    // create
    public function testShouldCreateService()
    {
        Category::factory()->create(['nama' => 'test']);
        $parameters = [
            'namaCustomer' => 'saitama',
            'noHp' => '085235690084',
            'bisaWA' => false,
            'namaProduk' => 'laptop testing',
            'kategori' => 'test',
            'keluhan' => 'lagi ditesting',
            'butuhPersetujuan' => false,
            'kelengkapan' => 'baterai',
            'catatan' => 'password e ora',
            'uangMuka' => '1000',
            'estimasiBiaya' => '2000',
            'cacatProduk' => 'baret'
        ];
        $this->post('/services', $parameters, ['Authorization' => 'Bearer ' . $this->getToken('customer service')]);
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
        Category::factory()->create(['nama' => 'test']);
        Product::factory()->create(['kategori' => 'test']);
        Customer::factory()->create();
        Service::factory()->create(['idProduct' => 1, 'idCustomer' => 1]);
        $parameters = [
            'namaCustomer' => 'goku',
            'noHp' => '085235690023',
            'bisaWA' => true,
            'namaProduk' => 'hp testing',
            'kategori' => 'test',
            'keluhan' => 'lagi ditest',
            'butuhPersetujuan' => true,
            'kelengkapan' => 'tas',
            'catatan' => 'passwordnya 123',
            'uangMuka' => '4000',
            'estimasiBiaya' => '7000',
            'cacatProduk' => 'mulus'
        ];
        $this->put('/services/1', $parameters, ['Authorization' => 'Bearer ' . $this->getToken('customer service')]);
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
        Customer::factory()->count(3)->create();
        Product::factory()->count(3)->create();
        Service::factory()->count(3)->sequence(fn ($sequence) => ['idCustomer' => $sequence->index + 1, 'idProduct' => $sequence->index + 1])->create();
        $this->get('/services', ['Authorization' => 'Bearer ' . $this->getToken('customer service')]);
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
        $token = $this->getToken('teknisi');
        $category = Category::factory()->count(3)->create();
        $categoryArr = $category->toArray();
        Responbility::factory()->count(3)->sequence(fn ($sequence) => ['idKategori' => $sequence->index + 1])->create(['username' => '2211001']);
        Product::factory()->count(3)->sequence(function ($sequence) use ($categoryArr) {
            return ['kategori' => $categoryArr[$sequence->index]['nama']];
        })->create();
        Service::factory()->count(3)->sequence(fn ($sequence) => ['idProduct' => $sequence->index + 1])->create(['status' => 'antri', 'usernameTeknisi' => null]);
        $header = ['Authorization' => 'Bearer ' . $token];
        $this->get('/services/2211001/queue', $header);
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
        $token = $this->getToken('teknisi');
        Product::factory()->count(3)->create();
        Service::factory()->count(3)->sequence(fn ($sequence) => ['idProduct' => $sequence->index + 1])->create(['usernameTeknisi' => '2211001']);
        $header = ['Authorization' => 'Bearer ' . $token];
        $this->get('/services/2211001/progress', $header);
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
        Service::factory()->count(3)->create(['status' => 'antri']);
        $parameters = ['status' => 'mulai diagnosa'];
        $header = ['Authorization' => 'Bearer ' . $this->getToken('teknisi')];
        $this->put('/services/1/status', $parameters, $header);
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
        Service::factory()->count(3)->create();
        $header = ['Authorization' => 'Bearer ' . $this->getToken('pemilik')];
        $this->put('/services/1/confirm-cost', [], $header);
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
        Service::factory()->count(3)->create();
        $parameters = ['garansi' => '1 bulan'];
        $header = ['Authorization' => 'Bearer ' . $this->getToken('pemilik')];
        $this->put('/services/1/warranty', $parameters, $header);
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
        Service::factory()->count(3)->create();
        $parameters = ['disetujui' => true];
        $header = ['Authorization' => 'Bearer ' . $this->getToken('pemilik')];
        $this->put('/services/1/confirmation', $parameters, $header);
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
        Service::factory()->count(3)->create(['garansi' => '1 bulan']);
        $header = ['Authorization' => 'Bearer ' . $this->getToken('customer service')];
        $this->put('/services/1/take', [], $header);
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
        Service::factory()->for(Customer::factory(), 'klien')->for(Product::factory(), 'produk')->has(Broken::factory()->count(3), 'kerusakan')->has(History::factory()->count(3), 'riwayat')->create();
        $header = ['Authorization' => 'Bearer ' . $this->getToken('pemilik')];
        $this->delete('/services/1', $header);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'status',
            'message'
        ]);
    }

    // get service by id with brokens,customer and product
    public function testShouldreturnServiceWithAllRelation()
    {
        Service::factory()->for(Customer::factory(), 'klien')->for(Product::factory(), 'produk')->has(Broken::factory()->count(3), 'kerusakan')->create();
        $header = ['Authorization' => 'Bearer ' . $this->getToken('customer service')];
        $this->get('/services/1/detail?include=klien,produk,kerusakan', $header);
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
        Service::factory()->for(Customer::factory(), 'klien')->for(Product::factory(), 'produk')->has(Broken::factory()->count(3), 'kerusakan')->create();
        $header = ['Authorization' => 'Bearer ' . $this->getToken('customer service')];
        $this->get('/services/1/detail?include=klien', $header);
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
        Service::factory()->for(Customer::factory(), 'klien')->for(Product::factory(), 'produk')->has(Broken::factory()->count(3), 'kerusakan')->create();
        $header = ['Authorization' => 'Bearer ' . $this->getToken('customer service')];
        $this->get('/services/1/detail?include=produk', $header);
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
        Service::factory()->for(Customer::factory(), 'klien')->for(Product::factory(), 'produk')->has(Broken::factory()->count(3), 'kerusakan')->create();
        $header = ['Authorization' => 'Bearer ' . $this->getToken('customer service')];
        $this->get('/services/1/detail?include=kerusakan', $header);
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
        Service::factory()->for(Customer::factory(), 'klien')->for(Product::factory(), 'produk')->has(Broken::factory()->count(3), 'kerusakan')->create();
        $header = ['Authorization' => 'Bearer ' . $this->getToken('customer service')];
        $this->get('/services/1/detail', $header);
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
        Service::factory()->for(Customer::factory(), 'klien')->for(Product::factory(), 'produk')->has(Broken::factory()->count(3), 'kerusakan')->has(History::factory()->count(3), 'riwayat')->create();
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
    }
}