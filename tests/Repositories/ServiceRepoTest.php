<?php

use App\Models\Broken;
use App\Models\Category;
use App\Models\Customer;
use App\Models\History;
use App\Models\Product;
use App\Models\Responbility;
use App\Models\Service;
use Illuminate\Support\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ServiceRepoTest extends TestCase
{

    use DatabaseMigrations;

    private $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make('App\Repositories\ServiceRepository');
    }

    public function testShouldGetListService()
    {
        $customer = Customer::factory()->count(3)->create();
        $product = Product::factory()->count(3)->create();
        foreach ($product as $key => $item) {
            Service::factory()->count($key + 1)->for($customer[$key], 'klien')->for($product[$key], 'produk')->create();
        }
        $result = $this->repository->getListData();
        $service = Service::with('klien', 'produk')->orderByDesc('id')->get();
        $this->assertEquals($service->toArray(), $result->toArray());
        $this->assertEquals($service[0]->klien->toArray(), $result[0]->klien->toArray());
        $this->assertEquals($service[0]->produk->toArray(), $result[0]->produk->toArray());
    }

    public function testShouldGetSingleServiceByIdWithCustomerProductAndBrokens()
    {
        $customer = Customer::factory()->count(3)->create();
        $product = Product::factory()->count(3)->create();
        $i = 1;
        foreach ($customer as $key => $item) {
            Service::factory()->for($customer[$key], 'klien')->for($product[$key], 'produk')->has(Broken::factory()->count($i++), 'kerusakan')->create();
        }
        $service = Service::with(['klien', 'produk', 'kerusakan' => function ($q) {
            $q->orderByDesc('id');
        }])->where('id', 2)->first();
        $result = $this->repository->getDataWithRelationById(2);
        $this->assertEquals($service->toArray(), $result->toArray());
        $this->assertEquals($service->klien->toArray(), $result->klien->toArray());
        $this->assertEquals($service->produk->toArray(), $result->produk->toArray());
        $this->assertEquals($service->kerusakan->toArray(), $result->kerusakan->toArray());
    }

    public function testShouldGetListServiceQueue()
    {
        $category = Category::factory()->count(5)->create();
        $status = ['proses', 'antri', 'antri', 'antri', 'selesai'];
        $product = Product::factory()->create(['kategori' => 'wortel']);
        Service::factory()->for($product, 'produk')->create(['status' => 'antri']);
        foreach ($category as $key => $item) {
            $product = Product::factory()->create(['kategori' => $item->nama]);
            Service::factory()->for($product, 'produk')->create([
                'status' => $status[$key]
            ]);
            $responbility['kategori'][$key] = $item->toArray();
        }
        $product = Product::factory()->create(['kategori' => 'bayam']);
        Service::factory()->for($product, 'produk')->create(['status' => 'antri']);
        $service = Service::with('produk')->whereIn('id', [3, 4, 5])->orderByDesc('id')->get();
        $result = $this->repository->getListDataQueue($responbility);
        $this->assertEquals($service->toArray(), $result->toArray());
    }

    public function testShouldGetListProgressService()
    {
        $usernames = ['2211001', '2211002', '2211003'];
        foreach ($usernames as $key => $username) {
            $product = Product::factory()->create();
            Service::factory()->count(3)->for($product, 'produk')->create(['usernameTeknisi' => $username]);
        }
        $service = Service::with('produk')->whereIn('id', [4, 5, 6])->orderByDesc('id')->get();
        $result = $this->repository->getListDataMyProgress('2211002');
        $this->assertEquals($service->toArray(), $result->toArray());
    }

    public function testShouldgetDataByCode()
    {
        $code = ['221108001', '221108002', '221108003'];
        $products = Product::factory()->count(3)->create();
        foreach ($products as $key => $product) {
            Service::factory()->for($product, 'produk')->has(Broken::factory()->count(3), 'kerusakan')->has(History::factory()->count(4), 'riwayat')->create(['kode' => $code[$key]]);
        }
        $service = Service::with(['produk', 'kerusakan' => function ($q) {
            $q->orderByDesc('id');
        }, 'riwayat' => function ($q) {
            $q->orderByDesc('id');
        }])->where('kode', '221108002')->first();
        $result = $this->repository->getDataByCode('221108002');

        $this->assertEquals($service->toArray(), $result->toArray());
    }

    public function testShouldSetCodeServiceInSingleServiceDataById()
    {
        $date = Carbon::now('GMT+7');
        $code = $date->format('y') . $date->format('m') . $date->format('d') . sprintf("%03d", 2);
        Service::factory()->count(3)->create(['kode' => null]);
        $result = $this->repository->setCodeService(2);
        $service = Service::find(2);
        $this->assertEquals(true, $result);
        $this->assertEquals($code, $service->kode);
    }
}