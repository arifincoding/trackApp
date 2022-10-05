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
        $query = $this->serviceRepository->getListData($inputs);
        $fractal = new Manager();
        $data = $fractal->createData(new Collection($query, new ServicesTransformer))->toArray();
        return $data;
    }

    public function getServiceById(array $inputs, int $id): array
    {
        $query = $this->serviceRepository->getDataWithRelationById($id);
        $fractal = new Manager();
        if (isset($inputs['include'])) {
            $fractal->parseIncludes($inputs['include']);
        }
        $data = $fractal->createData(new Item($query, new ServicedetailTransformer))->toArray();
        return $data;
    }

    public function getServiceQueue(array $inputs, string $username): array
    {
        $resp = $this->responbilityRepository->getListDataByUsername($username);
        $query = $this->serviceRepository->getListDataQueue($resp, $inputs);
        $fractal = new Manager();
        $data = $fractal->createData(new Collection($query, new ServicequeueTransformer))->toArray();
        return $data;
    }

    public function getProgressService(array $inputs, string $username): array
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
        $this->serviceValidator->validate($inputs);
        $input = $this->inputsParse($inputs);
        $input['service'] += [
            'idCustomer' => $this->customerRepository->create($input['customer']),
            'idProduct' => $this->productRepository->create($input['product'])
        ];
        $data = $this->serviceRepository->create($input['service']);
        $this->serviceRepository->setCodeService($data['idService']);
        return $data;
    }

    public function updateServiceById(array $inputs, int $id): array
    {
        $this->serviceValidator->validate($inputs);
        $find = $this->serviceRepository->findDataById($id);
        $input = $this->inputsParse($inputs);
        $this->customerRepository->save($input['customer'], $find->idCustomer);
        $this->productRepository->save($input['product'], $find->idProduct);
        $data = $this->serviceRepository->save($input['service'], $id);
        return ['idService' => $data->id];
    }

    public function updateServiceStatus(array $inputs, int $id): array
    {
        $this->serviceValidator->statusService();
        $this->serviceValidator->validate($inputs);
        $inputs['usernameTeknisi'] = Auth::payload()->get('username');
        $data = $this->serviceRepository->update($inputs, $id);
        return $data;
    }

    public function setServiceTake(int $id): array
    {
        $find = $this->serviceRepository->findDataById($id);
        if ($find->garansi === null) {
            return [
                'success' => false,
                'message' => 'garansi perbaikan belum di tentukan'
            ];
        }
        $data = $this->serviceRepository->setDataTake($id);
        return [
            'success' => true,
            'data' => $data
        ];
    }

    public function setServiceConfirmCost(int $id): array
    {
        $find = $this->brokenRepository->findDataByIdService($id, 'biaya');
        if ($find !== null) {
            return [
                'success' => false,
                'message' => 'data kerusakan masih ada yang belum diberi biaya'
            ];
        }
        $brokens = $this->brokenRepository->getListDataByIdService($id);
        $total = 0;
        foreach ($brokens as $item) {
            $total += $item->biaya;
        }
        $inputs = ['konfirmasibiaya' => true, 'totalBiaya' => $total];
        $data = $this->serviceRepository->update($inputs, $id);
        return [
            'success' => true,
            'data' => $data
        ];
    }

    public function updateServiceWarranty(array $inputs, int $id): array
    {
        $this->serviceValidator->serviceWarranty();
        $this->serviceValidator->validate($inputs);
        $data = $this->serviceRepository->update($inputs, $id);
        return $data;
    }

    public function setServiceConfirmation(array $inputs, int $id): array
    {
        $this->serviceValidator->confirmation($inputs);
        $this->serviceValidator->validate($inputs);
        $find = $this->brokenRepository->findDataByIdService($id, 'disetujui');
        if ($find !== null) {
            return [
                'success' => false,
                'message' => 'data kerusakan masih ada yang belum diberi persetujuan'
            ];
        }
        $brokens = $this->brokenRepository->getListDataByIdService($id, ['disetujui' => 1]);
        $total = 0;
        foreach ($brokens as $item) {
            $total += $item->biaya;
        }
        $inputs['totalBiaya'] = $total;
        $data = $this->serviceRepository->update($inputs, $id);
        $this->brokenRepository->setCostInNotAgreeToZero($id);
        return [
            'success' => true,
            'data' => $data
        ];
    }

    public function deleteServiceById(int $id): string
    {
        $find = $this->serviceRepository->findDataById($id);
        $this->customerRepository->delete($find->idCustomer);
        $this->productRepository->delete($find->idProduct);
        $this->serviceRepository->delete($id);
        $this->historyRepository->deleteByIdService($id);
        $this->brokenRepository->deleteByIdService($id);
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