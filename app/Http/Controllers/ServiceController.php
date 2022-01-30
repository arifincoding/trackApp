<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Repositories\ServiceRepository;
use App\Validations\ServiceValidation;
use App\Validations\WarrantyValidation;
use App\Repositories\ResponbilityRepository;

class ServiceController extends Controller{
    
    function __construct(ServiceRepository $service){
        $this->repository = $service;
    }

    function getListService(){
        $data = $this->repository->getListData();
        return $this->jsonSuccess('sukses',200,$data['data']);
    }

    function getServiceById($id){
        $data = $this->repository->getDataById($id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    function getServiceQueue(ResponbilityRepository $resp){
        $data = $this->repository->getDataQueue($resp);
        return $this->jsonSuccess('sukses',200,$data);
    }

    function getProgressService(){
        $data = $this->repository->getListDataByTechUsername(auth()->payload()->get('username'));
        return $this->jsonSuccess('sukses',200,$data);
    }

    function newService(Request $request, ServiceValidation $validator):JsonResponse
    {
        $validator->post();
        $validation = $validator->validate($request->all());
        $customerExist = false;
        if(!empty($request->input('noHp'))){
            $customerExist = $this->repository->customer->isCustomerExist($request->only(['namaCustomer','noHp']));
        }
        if($customerExist === false){
            $dataCustomer = $this->repository->customer->create($request->all());
            $idCustomer = $dataCustomer['idCustomer'];
        }else{
            $idCustomer = $customerExist['idCustomer'];
        }
        $dataService = $this->repository->create($request->all(),$idCustomer);
        return $this->jsonSuccess('sukses',200,$dataService);
    }

    public function updateService(Request $request, $id, ServiceValidation $validator){
        $validator->post();
        $validation = $validator->validate($request->all());
        $dataCustomer = $this->repository->customer->update($request->all(), $id);
        $data = $this->repository->update($request->all(),$dataCustomer['idCustomer'],$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function updateServiceStatus(Request $request,$id){
        $data = $this->repository->updateDataStatus($request->all(),$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function deleteService($id){
        $this->repository->customer->deleteById($id);
        $data = $this->repository->deleteById($id);
        return $this->jsonSUccess('sukses',200,$data);
    }
}

?>