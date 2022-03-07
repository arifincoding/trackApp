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

use Illuminate\Database\Eloquent\ModelNotFoundException;

class ServiceController extends Controller{
    
    function __construct(ServiceRepository $service, ServiceTrackRepository $serviceTrack, ResponbilityRepository $responbility, CustomerRepository $customer){
        $this->serviceRepository = $service;
        $this->serviceTrackRepository = $serviceTrack;
        $this->responbilityRepository = $responbility;
        $this->customerRepository = $customer;
    }

    function getListService(){
        $data = $this->serviceRepository->getListData();
        return $this->jsonSuccess('sukses',200,$data['data']);
    }

    function getServiceById($id){
        $data = $this->serviceRepository->getDataJoinCustomerById($id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    function getServiceQueue(){
        $resp = $this->responbilityRepository->getListDataByUsername(auth()->payload()->get('username'));
        if($resp){
            $data = $this->serviceRepository->getDataQueue($resp);
            return $this->jsonSuccess('sukses',200,$data);
        }
        throw new ModelNotFoundException();
    }

    function getProgressService(){
        $data = $this->serviceRepository->getListDataByTechUsername(auth()->payload()->get('username'));
        return $this->jsonSuccess('sukses',200,$data);
    }

    function newService(Request $request, ServiceValidation $validator):JsonResponse
    {
        $validator->post();
        $validation = $validator->validate($request->all());
        
        $customerExist = $this->customerRepository->isCustomerExist($request->only(['namaCustomer','noHp']));
        
        if($customerExist['exist'] === false){
            $dataCustomer = $this->customerRepository->create($request->all());
            $idCustomer = $dataCustomer['idCustomer'];
        }else{
            $dataCustomer = $this->customerRepository->updateCount($customerExist['idCustomer'],'plus');
            $idCustomer = $dataCustomer['idCustomer'];
        }
        
        $dataService = $this->serviceRepository->create($request->all(),$idCustomer);
        $this->addServiceTrack('antri',$dataService['idService']);
        return $this->jsonSuccess('sukses',200,$dataService);
    }

    public function updateService(Request $request, $id, ServiceValidation $validator){
        $validator->post();
        $validation = $validator->validate($request->all());
        $dataService = $this->serviceRepository->getDataById($id);
        if($dataService){
            $dataCustomer = $this->customerRepository->update($request->all(),$dataService['idCustomer']);
            $data = $this->serviceRepository->update($request->all(),$dataCustomer['idCustomer'],$id);
            return $this->jsonSuccess('sukses',200,$data);
        }
    }

    public function updateServiceStatus(Request $request,$id){
        $data = $this->serviceRepository->updateDataStatus($request->all(),$id);
        $this->addServiceTrack($request->input('status'),$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function updateServiceTake(Request $request, $id){
        $data = $this->serviceRepository->updateTake($request->all(),$id);
        $this->addServiceTrack('diambil',$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function updateServiceConfirmCost(Request $request, $id){
        $data = $this->serviceRepository->updateConfirmCost($request->all(),$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function updateServiceWarranty(Request $request, $id){
        $data = $this->serviceRepository->updateWarranty($request->all(),$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function updateServiceConfirmation(Request $request, $id){
        $data = $this->serviceRepository->updateConfirmation($request->all(),$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function deleteService($id){
        $dataService = $this->serviceRepository->getDataById($id);
        if($dataService){
            $dataCustomer = $this->customerRepository->findDataById($dataService['idCustomer']);
            if($dataCustomer['count'] > 1){
                $this->customerRepository->updateCount($dataCustomer['id'],'minus');
            }else{
                $this->customerRepository->deleteById($dataService['idCustomer']);
            }
            $data = $this->serviceRepository->deleteById($id);
            return $this->jsonSUccess('sukses',200,$data);
        }
    }

    private function addServiceTrack(string $status, string $id){
        $message = '';
        $service = $this->serviceRepository->getDataById($id);
        if($status=='antri'){
            $message = 'barang service masuk dan menunggu untuk di diagnosa';
        }else if($status === 'diagnosa'){
            $message = $service['category'].' anda sedang dalam proses diagnosa';
        }else if($status ==  'selesai diagnosa'){
            $message = $service['category'].' anda selesai di diagnosa';
        }else if($status ==  'proses'){
            $message = $service['category'].' anda sedang dalam proses perbaikan';
        }else if($status == 'selesai'){
            $message = $service['category'].' anda telah selesai diperbaiki';
        }else if($status == 'diambil'){
            $message = $service['category'].' anda telah diambil';
        }
        $attributs = [
            'idService'=>$id,
            'title'=>$message,
            'status'=>$status
        ];
        $this->serviceTrackRepository->create($attributs);
    }
}

?>