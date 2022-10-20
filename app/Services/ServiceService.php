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
use Illuminate\Support\Facades\Log;

class ServiceService implements ServiceServiceContract
{
    private $serviceRepository;
    private $historyRepository;
    private $responbilityRepository;
    private $customerRepository;
    private $brokenRepository;
    private $productRepository;
    private $serviceValidator;

    public function __construct(ServiceRepository $service, HistoryRepository $history, ResponbilityRepository $responbility, CustomerRepository $customer, BrokenRepository $broken, ProductRepository $product, ServiceValidation $validator)
    {
        $this->serviceRepository = $service;
        $this->historyRepository = $history;
        $this->responbilityRepository = $responbility;
        $this->customerRepository = $customer;
        $this->brokenRepository = $broken;
        $this->productRepository = $product;
        $this->serviceValidator = $validator;
    }

    public function getListService(array $inputs): array
    {
        Log::info("trying to access all service data", ["query" => $inputs]);
        $query = $this->serviceRepository->getListData($inputs);
        $fractal = new Manager();
        $data = $fractal->createData(new Collection($query, new ServicesTransformer))->toArray();
        Log::info("user is accessing all service data");
        return $data;
    }

    public function getServiceById(array $inputs, int $id): array
    {
        Log::info("User trying to accessing a single service data by id service", ['id service' => $id, "with" => $inputs]);
        $query = $this->serviceRepository->getDataWithRelationById($id);
        $fractal = new Manager();
        if (isset($inputs['include'])) {
            $fractal->parseIncludes($inputs['include']);
        }
        $data = $fractal->createData(new Item($query, new ServicedetailTransformer))->toArray();
        Log::info("User is accessing a single service data", ["id service" => $query->id]);
        return $data;
    }

    public function getServiceQueue(array $inputs, string $username): array
    {
        Log::info("User is trying to access alli service queue data by technician responbility");
        $resp = $this->responbilityRepository->getListDataByUsername($username);
        $data = [];
        if ($resp) {
            Log::info("list responbility data by username found for accessing service queue data by technician responbility", ['username' => $username, 'filters' => $inputs]);
            $query = $this->serviceRepository->getListDataQueue($resp, $inputs);
            Log::info("User is accessing all service queue data by technician responbility");
            $fractal = new Manager();
            $data = $fractal->createData(new Collection($query, new ServicequeueTransformer))->toArray();
        } else {
            Log::warning("User accessing all service queue data failed caused list responbilities data for this username not found", ['username' => $username]);
        }
        return $data;
    }

    public function getProgressService(array $inputs, string $username): array
    {
        Log::info("user trying to accessing all service progres data by technician username", ["username" => $username, "filters" => $inputs]);
        $data = [];
        $query = $this->serviceRepository->getListDataMyProgress($username, $inputs);
        Log::info("User is accessing all service progres data by technician username");
        $fractal = new Manager();
        $data = $fractal->createData(new Collection($query, new ServicequeueTransformer))->toArray();
        return $data;
    }

    public function getServiceTrack(string $code): array
    {
        Log::info("User trying to accessing a single service track data by code service", ["code service" => $code]);
        $query = $this->serviceRepository->getDataByCode($code);
        $data = [];
        $message = 'data tidak ditemukan';
        if ($query) {
            Log::info("User is accessing a single service track data by code service", ["code service" => $query->kode]);
            $message = 'sukses';
            $fractal = new Manager();
            $data = $fractal->createData(new Item($query, new ServicetrackTransformer))->toArray();
        } else {
            Log::warning("a single service track data by code service not found", ["code service" => $code]);
        }
        return [
            'message' => $message,
            'data' => $data
        ];
    }

    public function newService(array $inputs): array
    {
        Log::info("User is trying to create a single service data", ['data' => $inputs]);
        $this->serviceValidator->validate($inputs, 'create');
        $input = $this->inputsParse($inputs);
        $input['service'] += [
            'idCustomer' => $this->customerRepository->create($input['customer']),
            'idProduct' => $this->productRepository->create($input['product'])
        ];
        $data = $this->serviceRepository->create($input['service']);
        Log::info("User create a single service data successfully", ['id service' => $data]);
        $this->serviceRepository->setCodeService($data);
        Log::info("set code in the single service data by id service successfully", ['id service' => $data]);
        return ['idService' => $data];
    }

    public function updateServiceById(array $inputs, int $id): array
    {
        Log::info("user is trying to update a single service data by id service", ["id service" => $id, "data" => $inputs]);
        $this->serviceValidator->validate($inputs, 'update');
        $find = $this->serviceRepository->findById($id);
        Log::info("service data found for updating a single service data by id service", ["id service" => $find->id]);
        $input = $this->inputsParse($inputs);
        $this->customerRepository->save($input['customer'], $find->idCustomer);
        $this->productRepository->save($input['product'], $find->idProduct);
        $data = $this->serviceRepository->save($input['service'], $id);
        Log::info("User update a single service data by id service successfully", ["id service" => $data->id]);
        return ['idService' => $data->id];
    }

    public function updateServiceStatus(array $inputs, int $id): array
    {
        Log::info("user trying to update service status in a single service data by id service", ["id service" => $id, "data" => $inputs]);
        $this->serviceValidator->statusService();
        $this->serviceValidator->validate($inputs, 'updateStatus');
        $inputs['usernameTeknisi'] = Auth::payload()->get('username');
        $data = $this->serviceRepository->save($inputs, $id);
        Log::info("User update service status in a single service data by id service successfully", ["id service" => $data->id]);
        return ["idService" => $data->id];
    }

    public function setServiceTake(int $id): array
    {
        Log::info("user is trying to set taking service in a single service data by id service", ["id service" => $id]);
        $find = $this->serviceRepository->findById($id);
        Log::info("service data found for set a taking data in the single service data by id service", ["id service" => $find->id]);
        if ($find->garansi === null) {
            Log::warning("cannot set a taking data caused a warranty data in this service data is not set");
            return [
                'success' => false,
                'message' => 'garansi perbaikan belum di tentukan'
            ];
        }
        $data = $this->serviceRepository->setDataTake($id);
        Log::info("user set taking service in a single service data by id service successfully", ['id service' => $data]);
        return [
            'success' => true,
            'data' => ['idService' => $data]
        ];
    }

    public function setServiceConfirmCost(int $id): array
    {
        Log::info("User trying to set service confirmation cost in a single service data by id service", ["id service" => $id]);
        $find = $this->brokenRepository->findOneDataByWhere(['idService' => $id, 'biaya' => null]);
        if ($find !== null) {
            Log::warning("cannot set service confirmation cost caused a single broken data by id service and cost is null found", ["id service" => $id, 'id broken' => $find->id]);
            return [
                'success' => false,
                'message' => 'data kerusakan masih ada yang belum diberi biaya'
            ];
        }
        Log::info("a single broken data by id service and cost is null not found");
        $brokens = $this->brokenRepository->getListDataByIdService($id);
        Log::info("list broken data by id service found for set total service cost", ['id service' => $id]);
        $total = 0;
        foreach ($brokens as $item) {
            $total += $item->biaya;
        }
        $inputs = ['konfirmasibiaya' => true, 'totalBiaya' => $total];
        $data = $this->serviceRepository->save($inputs, $id);
        Log::info("set total service cost in a single service data by id service successfully", ['id service' => $data->id]);
        Log::info("user set confirmation cost in a single service data by id service successfully", ["id service" => $data->id]);
        return [
            'success' => true,
            'data' => ["idService" => $data->id]
        ];
    }

    public function updateServiceWarranty(array $inputs, int $id): array
    {
        Log::info("User trying to update service warranty in a single service data by id service", ["id service" => $id, "data" => $inputs]);
        $this->serviceValidator->serviceWarranty();
        $this->serviceValidator->validate($inputs, 'updateWarranty');
        $data = $this->serviceRepository->save($inputs, $id);
        Log::info("user update service warranty in a single service data by id service successfully", ["id service" => $data->id]);
        return ["idService" => $data->id];
    }

    public function setServiceConfirmation(array $inputs, int $id): array
    {
        Log::info("User trying to set service confirmation by id service", ["id service" => $id, 'data' => $inputs]);
        $this->serviceValidator->confirmation($inputs);
        $this->serviceValidator->validate($inputs, 'updateConfirmation');
        $find = $this->brokenRepository->findOneDataByWhere(['idService' => $id, 'disetujui' => null]);
        if ($find !== null) {
            Log::warning("cannot set service confirmation caused a single broken data by id service and agreed is null found", ['id broken' => $find->id]);
            return [
                'success' => false,
                'message' => 'data kerusakan masih ada yang belum diberi persetujuan'
            ];
        }
        Log::info("a single broken data by id service and agreed is null not found", ['id service' => $id]);
        $brokens = $this->brokenRepository->getListDataByIdService($id, ['disetujui' => 1]);
        Log::info("list broken data by id service and agreed is true found for set total service cost", ["id service" => $id]);
        $total = 0;
        foreach ($brokens as $item) {
            $total += $item->biaya;
        }
        $inputs['totalBiaya'] = $total;
        $data = $this->serviceRepository->save($inputs, $id);
        Log::info("set total service cost in a single service data by id service successfully", ['id service' => $data->id]);
        Log::info("user set a service confirmation in a single service data by id service successfully", ['id service' => $data->id]);
        $this->brokenRepository->setCostInNotAgreeToZero($id);
        Log::info("set all cost in broken data by id service and agreed false to zero successfully", ["id service" => $data->id]);
        return [
            'success' => true,
            'data' => ["idService" => $data->id]
        ];
    }

    public function deleteServiceById(int $id): string
    {
        Log::info("user trying to delete a single service data by id service", ["id service" => $id]);
        $find = $this->serviceRepository->findById($id);
        $this->customerRepository->delete($find->idCustomer);
        $this->productRepository->delete($find->idProduct);
        $this->serviceRepository->delete($id);
        $this->historyRepository->deleteByIdService($id);
        $this->brokenRepository->deleteByIdService($id);
        Log::info("User delete a single service data by id service successfully", ['id service' => $id]);
        return 'sukses hapus data service';
    }

    private function inputsParse(array $inputs): array
    {
        $noHp = $inputs['noHp'] ?? null;
        $wa = $inputs['bisaWA'];
        if ($noHp === null) {
            $wa = false;
        }
        return [
            'customer' => [
                'nama' => $inputs['namaCustomer'],
                'noHp' => $inputs['noHp'],
                'bisaWA' => $wa
            ],
            'product' => [
                'nama' => $inputs['namaProduk'],
                'kategori' => $inputs['kategori'],
                'kelengkapan' => $inputs['kelengkapan'],
                'catatan' => $inputs['catatan'],
                'cacatProduk' => $inputs['cacatProduk']
            ],
            'service' => [
                'keluhan' => $inputs['keluhan'],
                'butuhPersetujuan' => $inputs['butuhPersetujuan'],
                'uangMuka' => $inputs['uangMuka'],
                'estimasiBiaya' => $inputs['estimasiBiaya']
            ]
        ];
    }
}