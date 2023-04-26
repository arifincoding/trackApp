<?php

use App\Models\Broken;
use App\Models\Category;
use App\Models\Customer;
use App\Models\History;
use App\Models\Product;
use App\Models\Responbility;
use App\Models\Service;
use App\Models\User;
use App\Repositories\BrokenRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\HistoryRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ResponbilityRepository;
use App\Repositories\ServiceRepository;
use App\Services\ServiceService;
use App\Transformers\ServicedetailTransformer;
use App\Transformers\ServicequeueTransformer;
use App\Transformers\ServicesTransformer;
use App\Transformers\ServicetrackTransformer;
use App\Validations\ServiceValidation;
use Database\Factories\ResponbilityFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Testing\DatabaseTransactions;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class ServiceSrvTest extends TestCase
{
    use DatabaseTransactions;

    private $serviceRepository;
    private $historyRepository;
    private $customerRepository;
    private $productRepository;
    private $responbilityRepository;
    private $brokenRepository;
    private ServiceService $service;
    private $validator;

    public function setUp(): void
    {
        parent::setUp();
        $this->serviceRepository = $this->createMock(ServiceRepository::class);
        $this->historyRepository = $this->createMock(HistoryRepository::class);
        $this->customerRepository = $this->createMock(CustomerRepository::class);
        $this->productRepository = $this->createMock(ProductRepository::class);
        $this->responbilityRepository = $this->createMock(ResponbilityRepository::class);
        $this->brokenRepository = $this->createMock(BrokenRepository::class);
        $this->validator = $this->createMock(ServiceValidation::class);
        $this->service = new ServiceService($this->serviceRepository, $this->historyRepository, $this->responbilityRepository, $this->customerRepository, $this->brokenRepository, $this->productRepository, $this->validator);
    }

    public function testShouldGetListService()
    {
        Service::factory()->count(3)->create();
        $attributs = [
            'services.id',
            'code',
            'complaint',
            'status',
            'total_cost',
            'is_take',
            'is_approved',
            'customers.name as customer_name',
            'telp',
            'products.name as products_name',
            'categories.name as category'
        ];
        $services = Service::select($attributs)->join('products', 'services.product_id', 'products.id')->join('customers', 'products.customer_id', 'customers.id')->join('categories', 'products.category_id', 'categories.id')->orderByDesc('services.id')->get();
        $this->serviceRepository->expects($this->once())->method('getListData')->willReturn($services);
        $fractal = new Manager();
        $servicesFormatted = $fractal->createData(new Collection($services, new ServicesTransformer))->toArray();
        $result = $this->service->getListService();
        $this->assertEquals($servicesFormatted, $result);
    }

    public function testShouldGetSingleServiceDataById()
    {
        $serviceFactory = Service::factory()->create();
        $service = Service::with('product', 'product.client', 'product.category', 'broken')->where('id', $serviceFactory->id)->first();
        $this->serviceRepository->expects($this->once())->method('getDataWithRelationById')->willReturn($service);
        $fractal = new Manager();
        $serviceFormatted = $fractal->createData(new Item($service, new ServicedetailTransformer))->toArray();
        $result = $this->service->getServiceById($serviceFactory->id);
        $this->assertEquals($serviceFormatted, $result);
    }

    public function testShouldGetListServiceQueueByUsername()
    {
        $responbility = Responbility::factory()->create();
        Service::factory()->count(3)->create(['status' => 'antri']);
        $attributs = [
            'services.id',
            'code',
            'complaint',
            'status',
            'is_approved',
            'products.name as product_name',
            'categories.name as category'
        ];
        $this->responbilityRepository->expects($this->once())->method('findOneDataByUsername')->willReturn($responbility);
        $this->responbilityRepository->expects($this->once())->method('findOneByUsernameAndCategory')->willReturn(Responbility::with('category')->first());
        $service = Service::select($attributs)->join('products', 'services.product_id', 'products.id')->join('categories', 'products.category_id', 'categories.id')->orderByDesc('services.id')->get();
        $this->serviceRepository->expects($this->once())->method('getListDataQueue')->willReturn($service);
        $fractal = new Manager();
        $serviceQueue = $fractal->createData(new Collection($service, new ServicequeueTransformer))->toArray();
        $result = $this->service->getServiceQueue('2211001');
        $this->assertEquals($serviceQueue, $result);
    }

    public function testShouldGetProgresService()
    {
        Service::factory()->count(3)->create();
        $attributs = [
            'services.id',
            'services.code',
            'services.complaint',
            'services.status',
            'services.is_approved',
            'products.name as product_name',
            'categories.name as category'
        ];
        $service = Service::select($attributs)->join('products', 'services.product_id', 'products.id')->join('categories', 'products.category_id', 'categories.id')->orderByDesc('services.id')->get();
        $this->serviceRepository->expects($this->once())->method('getListDataMyProgress')->willReturn($service);
        $result = $this->service->getProgressService('2211001');
        $fractal = new Manager();
        $serviceProgres = $fractal->createData(new Collection($service, new ServicequeueTransformer))->toArray();
        $this->assertEquals($serviceProgres, $result);
    }

    public function testShouldGetServiceTrackByCode()
    {
        $code = '221115001';
        $serviceFactory = Service::factory()->create(['code' => $code]);
        $service = Service::with('product', 'product.client', 'product.category', 'broken', 'history')->where('id', $serviceFactory->id)->first();
        $this->serviceRepository->expects($this->once())->method('getDataByCode')->willReturn($service);
        $fractal = new Manager();
        $serviceTrack = $fractal->createData(new Item($service, new ServicetrackTransformer))->toArray();
        $result = $this->service->getServiceTrack($code);
        $this->assertEquals(['message' => 'sukses', 'data' => $serviceTrack], $result);
    }

    public function testShouldNewSingleServiceData()
    {
        $customer = Customer::factory()->make(['id' => 1]);
        $product = Product::factory()->make(['id' => 1, 'customer_id' => 1, 'category_id' => 1]);
        User::factory()->create(['username' => '2211001', 'password' => Hash::make('rahasia')]);
        Auth::attempt(['username' => '2211001', 'password' => 'rahasia']);
        $service = Service::factory()->make(['id' => 1, 'product_id' => 1]);
        $input = [
            'customer' => [
                'name' => $customer->name,
                'telp' => $customer->telp,
                'is_whatsapp' => $customer->is_whatsapp,
            ],
            'product' => [
                'name' => $product->name,
                'category_id' => 1,
                'completeness' => $product->completeness,
                'product_defects' => $product->product_defects
            ],
            'complaint' => $service->complaint,
            'need_approval' => $service->need_approval,
            'down_payment' => $service->down_payment,
            'estimated_cost' => $service->estimated_cost,
            'note' => $service->note,
        ];
        $this->validator->expects($this->once())->method('validate')->willReturn(true);
        $this->customerRepository->expects($this->once())->method('create')->willReturn($customer);
        $this->productRepository->expects($this->once())->method('create')->willReturn($product);
        $this->serviceRepository->expects($this->once())->method('create')->willReturn($service);
        $this->serviceRepository->expects($this->once())->method('setCodeService')->willReturn(true);
        $result = $this->service->newService($input);
        $this->assertEquals(['service_id' => 1], $result);
        Auth::logout();
    }

    public function testShouldUpdateSingleServiceDataById()
    {
        $customer = Customer::factory()->make(['id' => 2]);
        $product = Product::factory()->make(['id' => 3, 'customer_id' => 2, 'category_id' => 4]);
        $service = Service::factory()->make(['id' => 1, 'product_id' => 3]);
        $input = [
            'customer' => [
                'name' => $customer->name,
                'telp' => $customer->telp,
                'is_whatsapp' => $customer->is_whatsapp,
            ],
            'product' => [
                'name' => $product->name,
                'category_id' => 4,
                'completeness' => $product->completeness,
                'product_defects' => $product->product_defects
            ],
            'complaint' => $service->complaint,
            'need_approval' => $service->need_approval,
            'down_payment' => $service->down_payment,
            'estimated_cost' => $service->estimated_cost,
            'note' => $service->note,
        ];
        $this->validator->expects($this->once())->method('validate')->willReturn(true);
        $this->serviceRepository->expects($this->once())->method('findById')->willReturn($service);
        $this->customerRepository->expects($this->once())->method('save')->willReturn($customer);
        $this->productRepository->expects($this->once())->method('save')->willReturn($product);
        $this->serviceRepository->expects($this->once())->method('save')->willReturn($service);
        $result = $this->service->updateServiceById($input, 1);
        $this->assertEquals(['service_id' => 1], $result);
    }

    public function testShouldUpdateStatusInSingleServiceById()
    {
        User::factory()->create(['username' => '2211001', 'password' => Hash::make('rahasia')]);
        Auth::attempt(['username' => '2211001', 'password' => 'rahasia']);
        $input = ['status' => 'selesai'];
        $service = Service::factory()->sequence($input)->make(['id' => 1, 'product_id' => 2]);
        $this->validator->expects($this->once())->method('statusService');
        $this->validator->expects($this->once())->method('validate')->willReturn(true);
        $this->serviceRepository->expects($this->once())->method('save')->willReturn($service);
        $result =  $this->service->updateServiceStatus($input, 1);
        $this->assertEquals(['service_id' => 1], $result);
        Auth::logout();
    }

    public function testShouldSetServiceTakeInSingleServiceDataById()
    {
        $service = Service::factory()->make(['id' => 1, 'warranty' => '1 bulan', 'product_id' => 2]);
        $this->serviceRepository->expects($this->once())->method('findById')->willReturn($service);
        $this->serviceRepository->expects($this->once())->method('save')->willReturn($service);
        $result = $this->service->setServiceTake(1);
        $this->assertEquals(['success' => true, 'data' => ['service_id' => 1]], $result);
    }

    public function testShouldSetServiceConfirmCostInSingleServiceDataById()
    {
        $service = Service::factory()->make(['id' => 1, 'is_cost_confirmation' => true, 'product_id' => 2]);
        $this->brokenRepository->expects($this->once())->method('findOneDataByWhere')->willReturn(null);
        $this->brokenRepository->expects($this->once())->method('sumCostByServiceId')->willReturn(35000);
        $this->serviceRepository->expects($this->once())->method('save')->willReturn($service);
        $result = $this->service->setServiceConfirmCost(1);
        $this->assertEquals(['success' => true, 'data' => ['service_id' => 1]], $result);
    }

    public function testShouldUpdateServiceWarrantyInSingleServiceDataById()
    {
        $input = ['garansi' => '1 bulan'];
        $service = Service::factory()->sequence($input)->make(['id' => 1, 'product_id' => 2]);
        $this->validator->expects($this->once())->method('serviceWarranty');
        $this->validator->expects($this->once())->method('validate');
        $this->serviceRepository->expects($this->once())->method('save')->willReturn($service);
        $result = $this->service->updateServiceWarranty($input, 1);
        $this->assertEquals(['service_id' => 1], $result);
    }

    public function testShouldSetServiceConfirmationInSingleServiceDataById()
    {
        $input = ['is_approved' => true];
        $this->validator->expects($this->once())->method('confirmation');
        $this->validator->expects($this->once())->method('validate')->willReturn(true);
        $this->brokenRepository->expects($this->once())->method('findOneDataByWhere')->willReturn(null);
        $this->brokenRepository->expects($this->once())->method('sumCostByServiceId')->willReturn(35000);
        $service = Service::factory()->sequence($input)->make(['id' => 1, 'product_id' => 2]);
        $this->serviceRepository->expects($this->once())->method('save')->willReturn($service);
        $this->brokenRepository->expects($this->once())->method('setCostInNotAgreeToZero')->willReturn(true);
        $result = $this->service->setServiceConfirmation($input, 1);
        $this->assertEquals(['success' => true, 'data' => ['service_id' => 1]], $result);
    }

    public function testShouldDeleteServiceById()
    {
        $service = Service::factory()->make(['id' => 1, 'product_id' => 2]);
        $product = Product::factory()->make(['id' => 1, 'customer-id' => 3, 'category_id' => 4]);
        $this->serviceRepository->expects($this->once())->method('findById')->willReturn($service);
        $this->productRepository->expects($this->once())->method('findById')->willReturn($product);
        $this->customerRepository->expects($this->once())->method('delete')->willReturn(true);
        $this->productRepository->expects($this->once())->method('delete')->willReturn(true);
        $this->serviceRepository->expects($this->once())->method('delete')->willReturn(true);
        $this->historyRepository->expects($this->once())->method('deleteByIdService')->willReturn(true);
        $this->brokenRepository->expects($this->once())->method('deleteByIdService')->willReturn(true);
        $result = $this->service->deleteServiceById(1);
        $this->assertEquals('data service berhasil dihapus', $result);
    }
}
