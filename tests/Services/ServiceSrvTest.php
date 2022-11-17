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
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Testing\DatabaseMigrations;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class ServiceSrvTest extends TestCase
{
    use DatabaseMigrations;

    private ServiceRepository $serviceRepository;
    private HistoryRepository $historyRepository;
    private CustomerRepository $customerRepository;
    private ProductRepository $productRepository;
    private ResponbilityRepository $responbilityRepository;
    private BrokenRepository $brokenRepository;
    private ServiceService $service;
    private ServiceValidation $validator;

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
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $services = Service::factory()->count(3)->for($customer, 'klien')->for($product, 'produk')->create();
        $this->serviceRepository->expects($this->once())->method('getListData')->willReturn($services);
        $fractal = new Manager();
        $servicesFormatted = $fractal->createData(new Collection($services, new ServicesTransformer))->toArray();
        $result = $this->service->getListService();
        $this->assertEquals($servicesFormatted, $result);
    }

    public function testShouldGetSingleServiceDataById()
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $service = Service::factory()->for($customer, 'klien')->for($product, 'produk')->has(Broken::factory()->count(3), 'kerusakan')->create();
        $this->serviceRepository->expects($this->once())->method('getDataWithRelationById')->willReturn($service);
        $fractal = new Manager();
        $serviceFormatted = $fractal->createData(new Item($service, new ServicedetailTransformer))->toArray();
        $result = $this->service->getServiceById(1);
        $this->assertEquals($serviceFormatted, $result);
    }

    public function testShouldGetListServiceQueueByUsername()
    {
        $responbilities = Responbility::factory()->count(3)->for(Category::factory(), 'kategori')->create(['username' => '2211001']);
        $service = Service::factory()->count(3)->for(Product::factory(), 'produk')->create(['status' => 'antri']);

        $this->responbilityRepository->expects($this->once())->method('getListDataByUsername')->willReturn($responbilities);
        $this->serviceRepository->expects($this->once())->method('getListDataQueue')->willReturn($service);
        $fractal = new Manager();
        $serviceQueue = $fractal->createData(new Collection($service, new ServicequeueTransformer))->toArray();
        $result = $this->service->getServiceQueue('2211001');
        $this->assertEquals($serviceQueue, $result);
    }

    public function testShouldGetProgresService()
    {
        $service = Service::factory()->count(3)->for(Product::factory(), 'produk')->create(['usernameTeknisi' => '2211001']);
        $this->serviceRepository->expects($this->once())->method('getListDataMyProgress')->willReturn($service);
        $result = $this->service->getProgressService('2211001');
        $fractal = new Manager();
        $serviceProgres = $fractal->createData(new Collection($service, new ServicequeueTransformer))->toArray();
        $this->assertEquals($serviceProgres, $result);
    }

    public function testShouldGetServiceTrackByCode()
    {
        $code = '221115001';
        $service = Service::factory()->for(Product::factory(), 'produk')->has(Broken::factory()->count(3), 'kerusakan')->has(History::factory()->count(3), 'riwayat')->create(['kode' => $code]);
        $this->serviceRepository->expects($this->once())->method('getDataByCode')->willReturn($service);
        $fractal = new Manager();
        $serviceTrack = $fractal->createData(new Item($service, new ServicetrackTransformer))->toArray();
        $result = $this->service->getServiceTrack($code);
        $this->assertEquals(['message' => 'sukses', 'data' => $serviceTrack], $result);
    }

    public function testShouldNewSingleServiceData()
    {
        User::factory()->create(['username' => '2211001', 'password' => Hash::make('rahasia')]);
        Auth::attempt(['username' => '2211001', 'password' => 'rahasia']);
        $product = Product::factory()->create();
        $customer = Customer::factory()->create();
        $service = Service::factory()->for($product, 'produk')->for($customer, 'klien')->create();
        $input = [
            'namaCustomer' => $customer->nama,
            'noHp' => $customer->noHp,
            'bisaWA' => $customer->bisaWa,
            'namaProduk' => $product->nama,
            'kategori' => $product->kategori,
            'keluhan' => $service->keluhan,
            'butuhPersetujuan' => $service->butuhPersetujuan,
            'uangMuka' => $service->uangMuka,
            'estimasiBiaya' => $service->estimasiBiaya,
            'kelengkapan' => $product->kelengkapan,
            'catatan' => $product->catatan,
            'cacatProduk' => $product->cacatProduk
        ];
        $this->validator->expects($this->once())->method('validate')->willReturn(true);
        $this->customerRepository->expects($this->once())->method('create')->willReturn(1);
        $this->productRepository->expects($this->once())->method('create')->willReturn(1);
        $this->serviceRepository->expects($this->once())->method('save')->willReturn($service);
        $this->serviceRepository->expects($this->once())->method('setCodeService')->willReturn(true);
        $result = $this->service->newService($input);
        $this->assertEquals(['idService' => 1], $result);
        Auth::logout();
    }

    public function testShouldUpdateSingleServiceDataById()
    {
        $product = Product::factory()->make(['id' => 2]);
        $customer = Customer::factory()->make(['id' => 3]);
        $service = Service::factory()->for($product, 'produk')->for($customer, 'klien')->make(['id' => 1]);
        $input = [
            'namaCustomer' => $customer->nama,
            'noHp' => $customer->noHp,
            'bisaWA' => $customer->bisaWa,
            'namaProduk' => $product->nama,
            'kategori' => $product->kategori,
            'keluhan' => $service->keluhan,
            'butuhPersetujuan' => $service->butuhPersetujuan,
            'uangMuka' => $service->uangMuka,
            'estimasiBiaya' => $service->estimasiBiaya,
            'kelengkapan' => $product->kelengkapan,
            'catatan' => $product->catatan,
            'cacatProduk' => $product->cacatProduk
        ];
        $this->validator->expects($this->once())->method('validate')->willReturn(true);
        $this->serviceRepository->expects($this->once())->method('findById')->willReturn($service);
        $this->customerRepository->expects($this->once())->method('save')->willReturn($customer);
        $this->productRepository->expects($this->once())->method('save')->willReturn($product);
        $this->serviceRepository->expects($this->once())->method('save')->willReturn($service);
        $result = $this->service->updateServiceById($input, 1);
        $this->assertEquals(['idService' => 1], $result);
    }

    public function testShouldUpdateStatusInSingleServiceById()
    {
        User::factory()->create(['username' => '2211001', 'password' => Hash::make('rahasia')]);
        Auth::attempt(['username' => '2211001', 'password' => 'rahasia']);
        $input = ['status' => 'selesai', 'id' => 1];
        $service = Service::factory()->make($input);
        unset($input['id']);
        $this->validator->expects($this->once())->method('statusService');
        $this->validator->expects($this->once())->method('validate')->willReturn(true);
        $this->serviceRepository->expects($this->once())->method('save')->willReturn($service);
        $result =  $this->service->updateServiceStatus($input, 1);
        $this->assertEquals(['idService' => 1], $result);
        Auth::logout();
    }

    public function testShouldSetServiceTakeInSingleServiceDataById()
    {
        $service = Service::factory()->make(['id' => 1, 'garansi' => '1 bulan', 'disetujui' => true, 'konfirmasiBiaya' => true]);
        $this->serviceRepository->expects($this->once())->method('findById')->willReturn($service);
        $service->diambil = true;
        $service->waktuAmbil = Carbon::now('GMT+7');
        $this->serviceRepository->expects($this->once())->method('save')->willReturn($service);
        $result = $this->service->setServiceTake(1);
        $this->assertEquals(['success' => true, 'data' => ['idService' => 1]], $result);
    }

    public function testShouldSetServiceConfirmCostInSingleServiceDataById()
    {
        $service = Service::factory()->make(['id' => 1, 'konfirmasiBiaya' => true]);
        $broken = Broken::factory()->count(3)->make(['idService' => 1]);
        $this->brokenRepository->expects($this->once())->method('findOneDataByWhere')->willReturn(null);
        $this->brokenRepository->expects($this->once())->method('getListDataByIdService')->willReturn($broken);
        $this->serviceRepository->expects($this->once())->method('save')->willReturn($service);
        $result = $this->service->setServiceConfirmCost(1);
        $this->assertEquals(['success' => true, 'data' => ['idService' => 1]], $result);
    }

    public function testShouldUpdateServiceWarrantyInSingleServiceDataById()
    {
        $service = Service::factory()->make(['id' => 1, 'garansi' => '1 bulan']);
        $this->validator->expects($this->once())->method('serviceWarranty');
        $this->validator->expects($this->once())->method('validate');
        $this->serviceRepository->expects($this->once())->method('save')->willReturn($service);
        $result = $this->service->updateServiceWarranty(['garansi' => '1 bulan'], 1);
        $this->assertEquals(['idService' => 1], $result);
    }

    public function testShouldSetServiceConfirmationInSingleServiceDataById()
    {
        $this->validator->expects($this->once())->method('confirmation');
        $this->validator->expects($this->once())->method('validate')->willReturn(true);
        $this->brokenRepository->expects($this->once())->method('findOneDataByWhere')->willReturn(null);
        $broken = Broken::factory()->count(3)->state(new Sequence(['biaya' => 200000], ['biaya' => 200000], ['biaya' => 100000]))->make(['idService' => 1]);
        $this->brokenRepository->expects($this->once())->method('getListDataByIdService')->willReturn($broken);
        $service = Service::factory()->make(['id' => 1, 'disetujui' => true, 'totalBiaya' => 500000]);
        $this->serviceRepository->expects($this->once())->method('save')->willReturn($service);
        $this->brokenRepository->expects($this->once())->method('setCostInNotAgreeToZero')->willReturn(true);
        $result = $this->service->setServiceConfirmation(['disetujui' => true], 1);
        $this->assertEquals(['success' => true, 'data' => ['idService' => 1]], $result);
    }

    public function testShouldDeleteServiceById()
    {
        $service = Service::factory()->make(['id' => 1]);
        $this->serviceRepository->expects($this->once())->method('findById')->willReturn($service);
        $this->customerRepository->expects($this->once())->method('delete')->willReturn(true);
        $this->productRepository->expects($this->once())->method('delete')->willReturn(true);
        $this->serviceRepository->expects($this->once())->method('delete')->willReturn(true);
        $this->historyRepository->expects($this->once())->method('deleteByIdService')->willReturn(true);
        $this->brokenRepository->expects($this->once())->method('deleteByIdService')->willReturn(true);
        $result = $this->service->deleteServiceById(1);
        $this->assertEquals('sukses hapus data service', $result);
    }
}