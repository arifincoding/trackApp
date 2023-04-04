<?php

use App\Models\Broken;
use App\Models\Customer;
use App\Models\History;
use App\Models\Product;
use App\Models\Responbility;
use App\Models\Service;
use App\Models\User;
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

        Service::factory()->count(3)->create();

        $attributs = [
            'services.id as service_id',
            'services.code',
            'services.complaint',
            'services.status',
            'services.total_cost',
            'services.is_take',
            'services.is_approved',
            'customers.name as customer_name',
            'customers.telp',
            'products.name as products_name',
            'categories.name as category'
        ];

        $service = Service::select($attributs)->join('products', 'services.product_id', 'products.id')->join('customers', 'products.customer_id', 'customers.id')->join('categories', 'products.category_id', 'categories.id')->orderByDesc('services.id')->get();

        $result = $this->repository->getListData();

        $this->assertEquals($service->toArray(), $result->toArray());
    }

    public function testShouldGetSingleServiceByIdWithCustomerProductAndBrokens()
    {

        $serviceFactory = Service::factory()->create();

        Broken::factory()->count(4)->create(['service_id' => $serviceFactory->id]);

        $result = $this->repository->getDataWithRelationById($serviceFactory->id);

        $service = Service::with(['product', 'product.client', 'product.category', 'broken' => function ($q) {
            $q->orderByDesc('id');
        }])->where('id', $serviceFactory->id)->first();

        $this->assertEquals($service->toArray(), $result->toArray());
    }

    public function testShouldGetListServiceQueue()
    {

        $serviceFactory = Service::factory()->count(3)->create(['tecnician_username' => null, 'status' => 'antri']);

        $attributs = [
            'services.id as service_id',
            'services.code',
            'services.complaint',
            'services.status',
            'services.is_approved',
            'products.name as product_name',
            'categories.name as category'
        ];

        $user = User::factory()->create(['role' => 'teknisi', 'username' => '30031999']);

        $product = Product::whereIn('id', [$serviceFactory[0]->product_id, $serviceFactory[1]->product_id])->get();

        Responbility::factory()->count(2)->sequence(['category_id' => $product[0]->category_id, 'username' => $user->username], ['category_id' => $product[1]->category_id, 'username' => $user->username])->create();

        $service = Service::select($attributs)->join('products', 'services.product_id', 'products.id')->join('categories', 'products.category_id', 'categories.id')->whereIn('services.id', [$serviceFactory[0]->id, $serviceFactory[1]->id])->orderByDesc('services.id')->get();

        $result = $this->repository->getListDataQueue($user->username);

        $this->assertEquals($service->toArray(), $result->toArray());
    }

    public function testShouldGetListProgressService()
    {

        $user = User::factory()->create(['role' => 'teknisi', 'username' => '30031999']);

        $service = Service::factory()->count(5)->sequence(
            ['status' => 'mulai diagnosa'],
            ['tecnician_username' => $user->username, 'status' => 'selesai diagnosa'],
            ['tecnician_username' => $user->username, 'status' => 'selesai'],
            ['status' => 'tunggu'],
            ['tecnician_username' => $user->username, 'status' => 'selesai']
        )->create();

        $attributs = [
            'services.id as service_id',
            'services.code',
            'services.complaint',
            'services.status',
            'services.is_approved',
            'products.name as product_name',
            'categories.name as category'
        ];

        $service = Service::select($attributs)->join('products', 'services.product_id', 'products.id')->join('categories', 'products.category_id', 'categories.id')->whereIn('services.id', [$service[1]->id, $service[2]->id, $service[4]->id])->orderByDesc('services.id')->get();

        $result = $this->repository->getListDataMyProgress('30031999');

        $this->assertEquals($service->toArray(), $result->toArray());
    }

    public function testShouldgetDataByCode()
    {
        $serviceFactory = Service::factory()->count(3)->create();
        Broken::factory()->count(4)->create(['service_id' => $serviceFactory[1]->id]);
        History::factory()->count(4)->create(['service_id' => $serviceFactory[1]->id]);
        $service = Service::with(['product', 'product.category', 'broken' => function ($q) {
            $q->orderByDesc('id');
        }, 'history' => function ($q) {
            $q->orderByDesc('id');
        }])->where('id', $serviceFactory[1]->id)->first();
        $result = $this->repository->getDataByCode($serviceFactory[1]->code);
        $this->assertEquals($service->toArray(), $result->toArray());
    }

    public function testShouldSetCodeServiceInSingleServiceDataById()
    {
        $serviceFactory = Service::factory()->count(3)->create(['code' => null]);
        $date = Carbon::now('GMT+7');
        $code = $date->format('y') . $date->format('m') . $date->format('d') . sprintf("%03d", $serviceFactory[1]->id);
        $result = $this->repository->setCodeService($serviceFactory[1]->id);
        $service = Service::find($serviceFactory[1]->id);
        $this->assertEquals(true, $result);
        $this->assertEquals($code, $service->code);
    }
}
