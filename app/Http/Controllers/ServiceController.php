<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
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
use App\Http\Controllers\Contracts\ServiceControllerContract;

class ServiceController extends Controller implements ServiceControllerContract
{

    private $serviceRepository;
    private $historyRepository;
    private $responbilityRepository;
    private $customerRepository;
    private $brokenRepository;
    private $productRepository;

    function __construct(ServiceRepository $service, HistoryRepository $history, ResponbilityRepository $responbility, CustomerRepository $customer, BrokenRepository $broken, ProductRepository $product)
    {
        $this->serviceRepository = $service;
        $this->historyRepository = $history;
        $this->responbilityRepository = $responbility;
        $this->customerRepository = $customer;
        $this->brokenRepository = $broken;
        $this->productRepository = $product;
    }

    function getListService(Request $request): JsonResponse
    {
        $filter = $request->only('kategori', 'status', 'cari');
        $query = $this->serviceRepository->getListData($filter);
        $fractal = new Manager();
        $data = $fractal->createData(new Collection($query, new ServicesTransformer))->toArray();
        return $this->jsonSuccess('sukses', 200, $data);
    }

    function getServiceById(Request $request, int $id): JsonResponse
    {
        $query = $this->serviceRepository->getDataWithRelationById($id);
        $fractal = new Manager();
        if ($request->query('include')) {
            $fractal->parseIncludes($request->query('include'));
        }
        $data = $fractal->createData(new Item($query, new ServicedetailTransformer))->toArray();
        return $this->jsonSuccess('sukses', 200, $data);
    }

    function getServiceQueue(Request $request, string $id): JsonResponse
    {
        $filter = $request->only('kategori', 'cari');
        $resp = $this->responbilityRepository->getListDataByUsername($id);
        $query = $this->serviceRepository->getListDataQueue($resp, $filter);
        $fractal = new Manager();
        $data = $fractal->createData(new Collection($query, new ServicequeueTransformer))->toArray();
        return $this->jsonSuccess('sukses', 200, $data);
    }

    function getProgressService(Request $request, string $id): JsonResponse
    {
        $filter = $request->only('status', 'cari', 'kategori');
        $query = $this->serviceRepository->getListDataMyProgress($id, $filter);
        $fractal = new Manager();
        $data = $fractal->createData(new Collection($query, new ServicequeueTransformer))->toArray();
        return $this->jsonSuccess('sukses', 200, $data);
    }

    public function getServiceTrack(string $id): JsonResponse
    {
        $query = $this->serviceRepository->getDataByCode($id);
        $data = [];
        $message = 'data tidak ditemukan';
        if ($query) {
            $message = 'sukses';
            $fractal = new Manager();
            $data = $fractal->createData(new Item($query, new ServicetrackTransformer))->toArray();
        }
        return $this->jsonSuccess($message, 200, $data);
    }

    function newService(Request $request, ServiceValidation $validator): JsonResponse
    {
        $validation = $validator->validate($request->all());
        $inputs = [
            'customer' => $request->only(['namaCustomer', 'noHp', 'bisaWA']),
            'product' => $request->only(['namaProduk', 'kategori', 'kelengkapan', 'catatan', 'cacatProduk']),
            'service' => $request->only(['keluhan', 'butuhPersetujuan', 'uangMuka', 'estimasiBiaya'])
        ];
        $inputs['service']['idCustomer'] = $this->customerRepository->create($inputs['customer']);
        $inputs['service']['idProduct'] = $this->productRepository->create($inputs['product']);
        $saveService = $this->serviceRepository->create($inputs['service']);
        $this->serviceRepository->setCodeService($saveService['idService']);
        return $this->jsonSuccess('sukses', 200, $saveService);
    }

    public function updateService(Request $request, int $id, ServiceValidation $validator): JsonResponse
    {
        $validation = $validator->validate($request->all());
        $inputs = [
            'customer' => $request->only(['namaCustomer', 'noHp', 'bisaWA']),
            'product' => $request->only(['namaProduk', 'kategori', 'kelengkapan', 'catatan', 'cacatProduk']),
            'service' => $request->only(['keluhan', 'butuhPersetujuan', 'uangMuka', 'estimasiBiaya'])
        ];
        $find = $this->serviceRepository->findDataById($id);
        $this->customerRepository->update($inputs['customer'], $find->idCustomer);
        $this->productRepository->update($inputs['product'], $find->idProduct);
        $saveService = $this->serviceRepository->update($inputs['service'], $id);
        return $this->jsonSuccess('sukses', 200, $saveService);
    }

    public function updateServiceStatus(Request $request, int $id, ServiceValidation $validator): JsonResponse
    {
        $input = $request->only('status');
        $validator->statusService();
        $validator->validate($input);
        $input['usernameTeknisi'] = Auth::payload()->get('username');
        $data = $this->serviceRepository->update($input, $id);
        return $this->jsonSuccess('sukses', 200, $data);
    }

    public function setServiceTake(int $id): JsonResponse
    {
        $find = $this->serviceRepository->findDataById($id);
        if ($find->garansi === null) {
            return $this->jsonValidationError('garansi perbaikan belum di tentukan');
        }
        $data = $this->serviceRepository->setDataTake($id);
        return $this->jsonSuccess('sukses', 200, $data);
    }

    public function setConfirmCost(int $id): JsonResponse
    {
        $find = $this->brokenRepository->findDataByIdService($id, 'biaya');
        if ($find !== null) {
            return $this->jsonValidationError('data kerusakan masih ada yang belum diberi biaya');
        }
        $brokens = $this->brokenRepository->getListDataByIdService($id);
        $total = 0;
        foreach ($brokens as $item) {
            $total += $item->biaya;
        }
        $input = ['konfirmasibiaya' => true, 'totalBiaya' => $total];
        $data = $this->serviceRepository->update($input, $id);
        return $this->jsonSuccess('sukses', 200, $data);
    }

    public function updateWarranty(Request $request, int $id, ServiceValidation $validator): JsonResponse
    {
        $input = $request->only('garansi');
        $validator->serviceWarranty();
        $validator->validate($input);
        $data = $this->serviceRepository->update($input, $id);
        return $this->jsonSuccess('sukses', 200, $data);
    }

    public function setConfirmation(Request $request, int $id, ServiceValidation $validator): JsonResponse
    {
        $input =  $request->only('disetujui');
        $validator->confirmation($input);
        $validator->validate($input);
        $find = $this->brokenRepository->findDataByIdService($id, 'disetujui');
        if ($find !== null) {
            return $this->jsonValidationError('data kerusakan masih ada yang belum diberi persetujuan');
        }
        $brokens = $this->brokenRepository->getListDataByIdService($id, ['disetujui' => 1]);
        $total = 0;
        foreach ($brokens as $item) {
            $total += $item->biaya;
        }
        $input['totalBiaya'] = $total;
        $data = $this->serviceRepository->update($input, $id);
        $this->brokenRepository->setCostInNotAgreeToZero($id);
        return $this->jsonSuccess('sukses', 200, $data);
    }

    public function deleteService(int $id): JsonResponse
    {
        $find = $this->serviceRepository->findDataById($id);
        $this->customerRepository->deleteById($find->idCustomer);
        $this->productRepository->deleteById($find->idProduct);
        $this->serviceRepository->deleteById($id);
        $this->historyRepository->deleteByIdService($id);
        $this->brokenRepository->deleteByIdService($id);
        return $this->jsonMessageOnly('sukses hapus data service');
    }
}