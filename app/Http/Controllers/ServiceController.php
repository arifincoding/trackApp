<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

// validation
use App\Validations\ServiceValidation;
use App\Validations\WarrantyValidation;

// repository
use App\Repositories\ServiceRepository;
use App\Repositories\ResponbilityRepository;
use App\Repositories\ServiceTrackRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\BrokenRepository;
use Illuminate\Support\Facades\Http;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Helpers\Formatter;

class ServiceController extends Controller{
    
    private $serviceRepository;
    private $serviceTrackRepository;
    private $responbilityRepository;
    private $customerRepository;
    private $brokenRepository;

    function __construct(ServiceRepository $service, ServiceTrackRepository $serviceTrack, ResponbilityRepository $responbility, CustomerRepository $customer, BrokenRepository $broken){
        $this->serviceRepository = $service;
        $this->serviceTrackRepository = $serviceTrack;
        $this->responbilityRepository = $responbility;
        $this->customerRepository = $customer;
        $this->brokenRepository = $broken;
    }

    function getListService(Request $request){
        $limit = $request->query('limit',0);
        $filter = $request->only('kategori','status','cari');
        $data = $this->serviceRepository->getListDataJoinCustomer($limit,$filter);
        return $this->jsonSuccess('sukses',200,$data['data']);
    }

    function getServiceById($id){
        $data = $this->serviceRepository->getDataJoinCustomerById($id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    function getServiceQueue(Request $request){
        $username = auth()->payload()->get('username');
        $filter = $request->only('limit','kategori','cari');
        $limit = $request->query('limit',0);
        $resp = $this->responbilityRepository->getListDataByUsername($username);
        if($resp){
            $data = $this->serviceRepository->getListDataQueue($resp, $limit, $filter);
            return $this->jsonSuccess('sukses',200,$data);
        }
        throw new ModelNotFoundException();
    }

    function getMyProgressService(Request $request){
        $username = auth()->payload()->get('username');
        $filter = $request->only('status','cari','kategori');
        $limit = $request->query('limit',0);
        $data = $this->serviceRepository->getListDataMyProgress($username,$limit,$filter);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function getServiceTrackByCode(string $id){
        $data = $this->serviceRepository->getDataByCode($id);
        if($data === []){
            return $this->jsonSuccess('data tidak ditemukan',200,[]);
        }
        $data['kerusakan'] = $this->brokenRepository->getAllByIdService($data['idService']);
        $data['riwayat'] = $this->serviceTrackRepository->getAllByIdService($data['idService']);
        return $this->jsonSuccess('sukses',200,$data);
    }

    function newService(Request $request, ServiceValidation $validator):JsonResponse
    {
        $validation = $validator->validate($request->all());

        $inputCustomer = $request->only(['noHp','bisaWA']);
        $inputCustomer['nama'] = $request->input('namaCustomer');
        $inputService = $request->only(['kategori','keluhan','butuhKonfirmasi','kelengkapan','catatan','uangMuka','estimasiBiaya','cacatProduk']);
        $inputService['nama'] = $request->input('namaProduk');

        $saveCustomer = $this->newCustomer($inputCustomer);
        $inputService['idCustomer'] = $saveCustomer['idCustomer'];
        $saveService = $this->serviceRepository->create($inputService);

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
        $inputCustomer = $request->only(['noHp','bisaWA']);
        $inputCustomer['nama'] = $request->input('namaCustomer');
        $findService = $this->serviceRepository->getDataById($id);
        $saveCustomer = $this->updateCustomer($inputCustomer,$findService['idCustomer']);
        $inputService = $request->only(['kategori','keluhan','butuhKonfirmasi','kelengkapan','catatan','uangMuka','estimasiBiaya','cacatProduk']);
        $inputService['nama'] = $request->input('namaProduk');
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
        $data = $this->serviceRepository->updateDataStatus($input,$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function setServiceTake(string $id){
        $data = $this->serviceRepository->setDataTake($id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function setServiceConfirmCost(string $id){
        $data = $this->serviceRepository->setDataConfirmCost($id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function updateServiceWarranty(Request $request, $id,ServiceValidation $validator){
        $input = $request->only('garansi');
        $validator->serviceWarranty();
        $validator->validate($input);
        $data = $this->serviceRepository->updateWarranty($input,$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function setServiceConfirmation(Request $request,string $id,ServiceValidation $validator){
        $input =  $request->only('dikonfirmasi');
        $validator->confirmation($input);
        $validator->validate($input);
        $data = $this->serviceRepository->setDataConfirmation($id,$input);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function deleteService($id){
        $dataService = $this->serviceRepository->getDataById($id);
        if($dataService){
            $dataCustomer = $this->customerRepository->findDataById($dataService['idCustomer']);
            if($dataCustomer['jumlahService'] > 1){
                $this->customerRepository->updateCount($dataCustomer['id'],'minus');
            }else{
                $this->customerRepository->deleteById($dataService['idCustomer']);
            }
            $data = $this->serviceRepository->deleteById($id);
            if($data['sukses']===true){
                $this->serviceTrackRepository->deleteByIdService($id);
                $this->brokenRepository->deleteByIdService($id);
            }
            return $this->jsonMessageOnly('sukses hapus data service');
        }
    }
}
?>