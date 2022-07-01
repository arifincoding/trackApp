<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

// validation
use App\Validations\ServiceValidation;

// repository
use App\Repositories\ServiceRepository;
use App\Repositories\ResponbilityRepository;
use App\Repositories\HistoryRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\BrokenRepository;
use App\Repositories\ProductRepository;

use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use App\Transformers\ServicesTransformer;
use App\Transformers\ServicequeueTransformer;
use App\Transformers\ServicedetailTransformer;
use App\Transformers\ServicetrackTransformer;

class ServiceController extends Controller{
    
    private $serviceRepository;
    private $historyRepository;
    private $responbilityRepository;
    private $customerRepository;
    private $brokenRepository;
    private $productRepository;

    function __construct(ServiceRepository $service, HistoryRepository $history, ResponbilityRepository $responbility, CustomerRepository $customer, BrokenRepository $broken, ProductRepository $product){
        $this->serviceRepository = $service;
        $this->historyRepository = $history;
        $this->responbilityRepository = $responbility;
        $this->customerRepository = $customer;
        $this->brokenRepository = $broken;
        $this->productRepository = $product;
    }

    function getListService(Request $request){
        $limit = $request->query('limit',0);
        $filter = $request->only('kategori','status','cari');
        $query = $this->serviceRepository->getListData($limit,$filter);
        $fractal = new Manager();
        $data = $fractal->createData(new Collection($query,new ServicesTransformer))->toArray();
        return $this->jsonSuccess('sukses',200,$data);
    }

    function getServiceById(Request $request,$id){
        $query = $this->serviceRepository->getDataWithRelationById($id);
        $fractal = new Manager();
        if($request->query('include')){
            $fractal->parseIncludes($request->query('include'));
        }
        $data = $fractal->createData(new Item($query, new ServicedetailTransformer))->toArray();
        return $this->jsonSuccess('sukses',200,$data);
    }

    function getServiceQueue(Request $request,int $id){
        $filter = $request->only('limit','kategori','cari');
        $limit = $request->query('limit',0);
        $resp = $this->responbilityRepository->getListDataByUsername($id);
        if($resp){
            $query = $this->serviceRepository->getListDataQueue($resp, $limit, $filter);
            $fractal = new Manager();
            $data = $fractal->createData(new Collection($query,new ServicequeueTransformer))->toArray();
            return $this->jsonSuccess('sukses',200,$data);
        }
        throw new ModelNotFoundException();
    }

    function getProgressService(Request $request,$id){
        $filter = $request->only('status','cari','kategori');
        $limit = $request->query('limit',0);
        $query = $this->serviceRepository->getListDataMyProgress($id,$limit,$filter);
        $fractal = new Manager();
        $data = $fractal->createData(new Collection($query,new ServicequeueTransformer))->toArray();
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function getServiceTrackByCode(string $id){
        $query = $this->serviceRepository->getDataByCode($id);
        if($query === null){
            return $this->jsonSuccess('permintaan sukses data tidak ditemukan',200,[]);
        }
        $fractal = new Manager();
        $data = $fractal->createData(new Item($query, new ServicetrackTransformer))->toArray();
        return $this->jsonSuccess('sukses',200,$data);
    }

    function newService(Request $request, ServiceValidation $validator):JsonResponse
    {
        $validation = $validator->validate($request->all());
        // customer
        $inputCustomer = $request->only(['noHp','bisaWA']);
        $inputCustomer['nama'] = $request->input('namaCustomer');
        $saveCustomer = $this->newCustomer($inputCustomer);
        // product
        $inputProduct = $request->only(['kategori','kelengkapan','catatan','cacatProduk']);
        $inputProduct['nama'] = $request->input('namaProduk');
        $saveProduct = $this->productRepository->create($inputProduct);
        // service
        $inputService = $request->only(['keluhan','butuhPersetujuan','uangMuka','estimasiBiaya']);
        $inputService['idCustomer'] = $saveCustomer['idCustomer'];
        $inputService['idProduct'] = $saveProduct['idProduk'];
        $saveService = $this->serviceRepository->create($inputService);
        $this->serviceRepository->setCodeService($saveService['idService']);
        return $this->jsonSuccess('sukses',200,$saveService);
    }

    private function newCustomer(array $input){
        $data = [];
        $checkData = $this->customerRepository->isCustomerExist($input);
        if($checkData['exist'] === false){
            $data = $this->customerRepository->create($input);
        }else{
            $data = $this->customerRepository->updateCount($checkData['idCustomer'],'plus');
            $this->customerRepository->update($input,$data['idCustomer']);
        }
        return $data;
    }

    public function updateService(Request $request, $id, ServiceValidation $validator){
        $validation = $validator->validate($request->all());
        $findService = $this->serviceRepository->findDataById($id);
        // customer
        $inputCustomer = $request->only(['noHp','bisaWA']);
        $inputCustomer['nama'] = $request->input('namaCustomer');
        $saveCustomer = $this->updateCustomer($inputCustomer,$findService->idCustomer);
        // product
        $inputProduct = $request->only(['kategori','kelengkapan','catatan','cacatProduk']);
        $inputProduct['nama'] = $request->input('namaProduk');
        $saveProduct = $this->productRepository->update($inputProduct,$findService->idProduct);
        // service
        $inputService = $request->only(['keluhan','butuhPersetujuan','uangMuka','estimasiBiaya',]);
        $inputService['idCustomer'] = $saveCustomer['idCustomer'];
        $saveService = $this->serviceRepository->update($inputService,$id);
        return $this->jsonSuccess('sukses',200,$saveService);
    }

    private function updateCustomer(array $input,$id){
        $findData = $this->customerRepository->findDataById($id);
        $checkData = $this->customerRepository->isCustomerExist($input);
        $data = ['idCustomer'=>$findData['id']];
        if($findData['jumlahService'] > 1){
            if($input['nama'] != $findData['nama'] || $input['noHp'] != $findData['noHp']){
                if($checkData['exist'] === true){
                    $data = $this->customerRepository->updateCount($checkData['idCustomer'],'plus');
                }else{
                    $data = $this->customerRepository->create($input);
                }
                $this->customerRepository->updateCount($id,'minus');
            }
        }else{
            if($input['nama'] != $findData['nama'] || $input['noHp'] != $findData['noHp']){
                if($checkData['exist'] === true){
                    $data = $this->customerRepository->updateCount($checkData['idCustomer'],'plus');
                    $this->customerRepository->deleteById($id);
                }
            }
        }
        $this->customerRepository->update($input,$data['idCustomer']);
        return $data;
    }

    public function updateServiceStatus(Request $request,$id, ServiceValidation $validator){
        $input = $request->only('status');
        $validator->statusService();
        $validator->validate($input);
        $input['usernameTeknisi']=auth()->payload()->get('username');
        $data = $this->serviceRepository->update($input,$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function setServiceTake(string $id){
        $data = $this->serviceRepository->setDataTake($id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function setServiceConfirmCost(string $id){
        $brokens = $this->brokenRepository->getListDataByIdService($id);
        $total = 0;
        foreach($brokens as $item){
            $total += $item->biaya;
        }
        $input = ['konfirmasibiaya'=>true,'totalBiaya'=>$total];
        $data = $this->serviceRepository->update($input,$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function updateServiceWarranty(Request $request, $id,ServiceValidation $validator){
        $input = $request->only('garansi');
        $validator->serviceWarranty();
        $validator->validate($input);
        $data = $this->serviceRepository->update($input,$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function setServiceConfirmation(Request $request,string $id,ServiceValidation $validator){
        $input =  $request->only('disetujui');
        $validator->confirmation($input);
        $validator->validate($input);
        $brokens = $this->brokenRepository->getListDataByIdService($id,['disetujui'=>1]);
        $total = 0;
        foreach($brokens as $item){
            $total += $item->biaya;
        }
        $input['totalBiaya'] = $total;
        $data = $this->serviceRepository->update($input,$id);
        $this->brokenRepository->setCostInNotAgreeToZero($id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function deleteService($id){
        $findService = $this->serviceRepository->findDataById($id);
        if($findService){
            $findCustomer = $this->customerRepository->findDataById($findService->idCustomer);
            if($findCustomer['jumlahService'] > 1){
                $this->customerRepository->updateCount($findCustomer['id'],'minus');
            }else{
                $this->customerRepository->deleteById($findService->idCustomer);
            }
            $this->productRepository->deleteById($findService->idProduct);
            $data = $this->serviceRepository->deleteById($id);
            if($data['sukses']===true){
                $this->historyRepository->deleteByIdService($id);
                $this->brokenRepository->deleteByIdService($id);
            }
            return $this->jsonMessageOnly('sukses hapus data service');
        }
    }
}
?>