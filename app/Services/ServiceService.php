<?php

namespace App\Services;

use App\Services\Contracts\ServiceServiceContract;
use App\Validations\ServiceValidation;
use App\Repositories\ServiceRepository;
use App\Repositories\ResponbilityRepository;
use App\Repositories\HistoryRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\BrokenRepository;
use App\Repositories\ProductRepository;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use App\Transformers\ServicesTransformer;
use App\Transformers\ServicequeueTransformer;
use App\Transformers\ServicedetailTransformer;
use App\Transformers\ServicetrackTransformer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ServiceService implements ServiceServiceContract
{
    private ServiceRepository $serviceRepository;
    private HistoryRepository $historyRepository;
    private ResponbilityRepository $responbilityRepository;
    private CustomerRepository $customerRepository;
    private BrokenRepository $brokenRepository;
    private ProductRepository $productRepository;
    private ServiceValidation $validator;

    public function __construct(ServiceRepository $service, HistoryRepository $history, ResponbilityRepository $responbility, CustomerRepository $customer, BrokenRepository $broken, ProductRepository $product, ServiceValidation $validator)
    {
        $this->serviceRepository = $service;
        $this->historyRepository = $history;
        $this->responbilityRepository = $responbility;
        $this->customerRepository = $customer;
        $this->brokenRepository = $broken;
        $this->productRepository = $product;
        $this->validator = $validator;
    }

    public function getListService(array $inputs = []): array
    {
        $query = $this->serviceRepository->getListData($inputs);
        $fractal = new Manager();
        $data = $fractal->createData(new Collection($query, new ServicesTransformer))->toArray();
        return $data;
    }

    public function getServiceById(int $id, array $inputs = []): array
    {
        $query = $this->serviceRepository->getDataWithRelationById($id);
        $fractal = new Manager();
        isset($inputs['include']) ? $fractal->parseIncludes($inputs['include']) : null;
        $data = $fractal->createData(new Item($query, new ServicedetailTransformer))->toArray();
        return $data;
    }

    public function getServiceQueue(string $username, array $inputs = []): array
    {
        $data = [];
        if ($this->responbilityRepository->findOneDataByUsername($username)) {
            $category = $inputs['category'] ?? null;
            if (!$this->responbilityRepository->findOneByUsernameAndCategory($username, $category)) {
                return $data;
            }
            $query = $this->serviceRepository->getListDataQueue($username, $inputs);
            $fractal = new Manager();
            $data = $fractal->createData(new Collection($query, new ServicequeueTransformer))->toArray();
        }
        return $data;
    }

    public function getProgressService(string $username, array $inputs = []): array
    {
        $query = $this->serviceRepository->getListDataMyProgress($username, $inputs);
        $fractal = new Manager();
        $data = $fractal->createData(new Collection($query, new ServicequeueTransformer))->toArray();
        return $data;
    }

    public function getServiceTrack(string $code): array
    {
        $query = $this->serviceRepository->getDataByCode($code);
        $data = [];
        $message = 'data tidak ditemukan';
        if ($query) {
            $message = 'sukses';
            $fractal = new Manager();
            $data = $fractal->createData(new Item($query, new ServicetrackTransformer))->toArray();
        }
        return [
            'message' => $message,
            'data' => $data
        ];
    }

    public function newService(array $inputs): array
    {
        $this->validator->validate($inputs, 'create');
        $inputs['customer']['is_whatsapp'] = $inputs['customer']['telp'] ? $inputs['customer']['is_whatsapp'] : false;
        DB::beginTransaction();
        $customer = $this->customerRepository->create($inputs['customer']);
        $product = $this->productRepository->create($inputs['product'], $customer->id);
        unset($inputs['customer'], $inputs['product']);
        $data = $this->serviceRepository->create($inputs, $product->id, Auth::payload()->get('username'));
        $this->serviceRepository->setCodeService($data->id);
        DB::commit();
        return ['service_id' => $data->id];
    }

    public function updateServiceById(array $inputs, int $id): array
    {
        $this->validator->validate($inputs, 'update');
        $inputs['customer']['is_whatsapp'] = $inputs['customer']['telp'] ? $inputs['customer']['is_whatsapp'] : false;
        DB::beginTransaction();
        $find = $this->serviceRepository->findById($id);
        $this->customerRepository->save($inputs['customer'], $find->idCustomer);
        $this->productRepository->save($inputs['product'], $find->idProduct);
        unset($inputs['customer'], $inputs['product']);
        $data = $this->serviceRepository->save($inputs, $id);
        DB::commit();
        return ['service_id' => $data->id];
    }

    public function updateServiceStatus(array $inputs, int $id): array
    {
        $this->validator->statusService();
        $this->validator->validate($inputs, 'updateStatus');
        $inputs['tecnician_username'] = Auth::payload()->get('username');
        $data = $this->serviceRepository->save($inputs, $id);
        return ["service_id" => $data->id];
    }

    public function setServiceTake(int $id): array
    {
        $find = $this->serviceRepository->findById($id);
        !$find->warranty ? abort(400, 'garansi perbaikan belum di tentukan') : null;
        $attributs = [
            'is_take' => true,
            'taked_at' => Carbon::now('GMT+7')
        ];
        $data = $this->serviceRepository->save($attributs, $id);
        return [
            'success' => true,
            'data' => ['service_id' => $data->id]
        ];
    }

    public function setServiceConfirmCost(int $id): array
    {
        $find = $this->brokenRepository->findOneDataByWhere(['service_id' => $id, 'cost' => null]);
        $find ? abort(400, 'data kerusakan masih ada yang belum diberi biaya') : null;
        $totalCost = $this->brokenRepository->sumCostByServiceId($id);
        $inputs = [
            'is_cost_confirmation' => true,
            'total_cost' => $totalCost
        ];
        $data = $this->serviceRepository->save($inputs, $id);
        return [
            'success' => true,
            'data' => ["service_id" => $data->id]
        ];
    }

    public function updateServiceWarranty(array $inputs, int $id): array
    {
        $this->validator->serviceWarranty();
        $this->validator->validate($inputs, 'updateWarranty');
        $data = $this->serviceRepository->save($inputs, $id);
        return ["service_id" => $data->id];
    }

    public function setServiceConfirmation(array $inputs, int $id): array
    {
        $this->validator->confirmation($inputs);
        $this->validator->validate($inputs, 'updateConfirmation');
        DB::beginTransaction();
        $find = $this->brokenRepository->findOneDataByWhere(['idService' => $id, 'disetujui' => null]);
        $find ? abort(400, 'data kerusakan masih ada yang belum diberi persetujuan') : null;
        $totalCost = $this->brokenRepository->sumCostByServiceId($id, ['is_approved' => true]);
        $inputs['total_cost'] = $totalCost;
        $data = $this->serviceRepository->save($inputs, $id);
        $this->brokenRepository->setCostInNotAgreeToZero($id);
        DB::commit();
        return [
            'success' => true,
            'data' => ["service_id" => $data->id]
        ];
    }

    public function deleteServiceById(int $id): string
    {
        DB::beginTransaction();
        $find = $this->serviceRepository->findById($id);
        $findProduct = $this->productRepository->findById($find->product_id);
        $this->customerRepository->delete($findProduct->customer_id);
        $this->productRepository->delete($find->product_id);
        $this->serviceRepository->delete($id);
        $this->historyRepository->deleteByIdService($id);
        $this->brokenRepository->deleteByIdService($id);
        DB::commit();
        return 'data service berhasil dihapus';
    }
}
