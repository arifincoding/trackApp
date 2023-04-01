<?php

use App\Models\Broken;
use App\Models\Category;
use App\Models\Customer;
use App\Models\History;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Support\Carbon;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ServiceRepoTest extends TestCase
{

    use DatabaseTransactions;

    private $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make('App\Repositories\ServiceRepository');
    }

    public function testShouldGetListService()
    {
        $this->markTestSkipped();
        // $customer = Customer::factory()->count(3)->create();
        // $product = Product::factory()->count(3)->create();
        // foreach ($product as $key => $item) {
        // Service::factory()->count($key + 1)->for($customer[$key], 'client')->for($product[$key], 'product')->create();
        // }
        $result = $this->repository->getListData(['status' => 'tunggu']);
        // $service = Service::with('client', 'product')->orderByDesc('id')->get();
        // $this->assertEquals($service->toArray(), $result->toArray());
        // $this->assertEquals($service[0]->client->toArray(), $result[0]->client->toArray());
        // $this->assertEquals($service[0]->product->toArray(), $result[0]->product->toArray());
        // echo $result[0]->customers->name;
        echo json_encode($result->toArray());
    }

    public function testShouldGetSingleServiceByIdWithCustomerProductAndBrokens()
    {
        $this->markTestSkipped();
        $customer = Customer::factory()->count(3)->create();
        $product = Product::factory()->count(3)->create();
        $i = 1;
        foreach ($customer as $key => $item) {
            Service::factory()->for($customer[$key], 'client')->for($product[$key], 'product')->has(Broken::factory()->count($i++), 'broken')->create();
        }
        $service = Service::with(['client', 'product', 'broken' => function ($q) {
            $q->orderByDesc('id');
        }])->where('id', 2)->first();
        $result = $this->repository->getDataWithRelationById(2);
        $this->assertEquals($service->toArray(), $result->toArray());
        $this->assertEquals($service->client->toArray(), $result->client->toArray());
        $this->assertEquals($service->product->toArray(), $result->product->toArray());
        $this->assertEquals($service->broken->toArray(), $result->broken->toArray());
    }

    public function testShouldGetListServiceQueue()
    {
        $this->markTestSkipped();
        // $category = Category::factory()->count(7)->create();
        // $status = ['antri', 'proses', 'antri', 'antri', 'antri', 'selesai', 'antri'];
        // Product::factory()->count(7)->sequence(function ($sequence) use ($category) {
        //     return ['category' => $category[$sequence->index]->name];
        // })->create();
        // Service::factory()->count(7)->sequence(function ($sequence) use ($status) {
        //     return ['product_id' => $sequence->index + 1, 'status' => $status[$sequence->index]];
        // });
        // $responbility = [];
        // $j = 0;
        // for ($i = 2; $i < 5; $i++) {
        //     $responbility[$j++]['category'] = ['name' => $category[$i]->id];
        // }
        // $service = Service::with('product')->whereIn('id', [3, 4, 5])->orderByDesc('id')->get();
        $result = $this->repository->getListDataQueue('30031999', ['search' => 'window']);
        echo json_encode($result->toArray());
        // $this->assertEquals($service->toArray(), $result->toArray());
    }

    public function testShouldGetListProgressService()
    {
        // $this->markTestSkipped();
        // $usernames = ['2211001', '2211002', '2211003'];
        // foreach ($usernames as $key => $username) {
        // $product = Product::factory()->create();
        // Service::factory()->count(3)->for($product, 'product')->create(['tecnician_username' => $username]);
        // }
        // $service = Service::with('product')->whereIn('id', [4, 5, 6])->orderByDesc('id')->get();

        $result = $this->repository->getListDataMyProgress('30031999', ['search' => 'mati', 'category' => 'motor', 'status' => 'antri']);
        echo json_encode($result->toArray());
        // $this->assertEquals($service->toArray(), $result->toArray());
    }

    public function testShouldgetDataByCode()
    {
        $this->markTestSkipped();
        $code = ['221108001', '221108002', '221108003'];
        $products = Product::factory()->count(3)->create();
        foreach ($products as $key => $product) {
            Service::factory()->for($product, 'product')->has(Broken::factory()->count(3), 'broken')->has(History::factory()->count(4), 'history')->create(['code' => $code[$key]]);
        }
        $service = Service::with(['product', 'broken' => function ($q) {
            $q->orderByDesc('id');
        }, 'history' => function ($q) {
            $q->orderByDesc('id');
        }])->where('code', '221108002')->first();
        $result = $this->repository->getDataByCode('221108002');

        $this->assertEquals($service->toArray(), $result->toArray());
    }

    public function testShouldSetCodeServiceInSingleServiceDataById()
    {
        $this->markTestSkipped();
        $date = Carbon::now('GMT+7');
        $code = $date->format('y') . $date->format('m') . $date->format('d') . sprintf("%03d", 2);
        Service::factory()->count(3)->create(['code' => null]);
        $result = $this->repository->setCodeService(2);
        $service = Service::find(2);
        $this->assertEquals(true, $result);
        $this->assertEquals($code, $service->code);
    }
}
