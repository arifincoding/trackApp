<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Repositories\WarrantyRepository;
use App\Validations\WarrantyValidation;

class WarrantyController extends Controller{

    function __construct(WarrantyRepository $repository){
        $this->repository = $repository;
    }

    function newWarrantyByIdService(Request $request, $id,WarrantyValidation $validator){
        $validation = $validator->validate($request->all());
        $data = $this->repository->create($request->all(), $id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    function getServiceWarrantyByIdService($id){
        $data = $this->repository->getListDataByIdService($id);
        return $this->jsonSuccess('sukses',200,$data);
    }
}