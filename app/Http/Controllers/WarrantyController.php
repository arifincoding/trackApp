<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Repositories\WarrantyRepository;
use App\Repositories\ServiceRepository;
use App\Validations\WarrantyValidation;

class WarrantyController extends Controller{

    public function __construct(WarrantyRepository $warranty, ServiceRepository $service){
        $this->warrantyRepository = $warranty;
        $this->serviceRepository = $service;
    }

    public function getServiceWarrantyByIdService($id){
        $data = $this->warrantyRepository->getListDataByIdService($id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function newWarrantyByIdService(Request $request, $id,WarrantyValidation $validator){
        $validation = $validator->validate($request->all());
        $this->serviceRepository->getDataById($id);
        $data = $this->warrantyRepository->create($request->all(), $id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function updateWarranty(Request $request, $id, WarrantyValidation $validator){
        $validation = $validator->validate($request->all());
        $data = $this->warrantyRepository->update($request->all(),$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function deleteWarranty($id){
        $data = $this->warrantyRepository->deleteById($request->all(),$id);
        return $this->jsonSuccess('sukses',200,$data);
    }
}