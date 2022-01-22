<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Repositories\ServiceRepository;
use App\Validations\ServiceValidation;
use App\Validations\WarrantyValidation;

class ServiceController extends Controller{
    
    function __construct(ServiceRepository $service){
        $this->repository = $service;
    }

    function getListService(){
        $data = $this->repository->getListData();
        return $this->jsonSuccess('sukses',200,$data['data']);
    }

    function newService(Request $request, ServiceValidation $validator):JsonResponse
    {
        $validator->post();
        $validation = $validator->validate($request->all());
        if($validation === true){
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
        return $this->jsonValidationError($validation);
    }

    function getServiceById($id){
        $data = $this->repository->getDataById($id);
        return $this->jsonSuccess('sukses',200,$data);
    }
}

?>