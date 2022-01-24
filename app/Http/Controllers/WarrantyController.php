<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Repositories\WarrantyRepository;
use App\Validations\WarrantyValidation;

class WarrantyController extends Controller{

    public function __construct(WarrantyRepository $repository){
        $this->repository = $repository;
    }

    public function getServiceWarrantyByIdService($id){
        $data = $this->repository->getListDataByIdService($id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function newWarrantyByIdService(Request $request, $id,WarrantyValidation $validator){
        $validation = $validator->validate($request->all());
        $data = $this->repository->create($request->all(), $id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function updateWarranty(Request $request, $id, WarrantyValidation $validator){
        $validation = $validator->validate($request->all());
        $data = $this->repository->update($request->all(),$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function deleteWarranty($id){
        $data = $this->repository->deleteById($request->all(),$id);
        return $this->jsonSuccess('sukses',200,$data);
    }
}