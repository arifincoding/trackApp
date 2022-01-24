<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\DiagnosaRepository;
use App\Validations\DiagnosaValidation;
use Illuminate\Http\JsonResponse;


class DiagnosaController extends Controller{

    public function __construct(DiagnosaRepository $repository){
        $this->repository = $repository;
    }

    public function getListDiagnosaByIdService($id){
        $data = $this->repository->getListDataByIdService($id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function newDiagnosaByIdService(Request $request,$id,DiagnosaValidation $validator){
        $validation = $validator->validate($request->only(['judul']));
        $data = $this->repository->create($request->all(),$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function getDiagnosaById($id){
        $data = $this->repository->getDataById($id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function updateDiagnosa(Request $request, $id, DiagnosaValidation $validator){
        $validation = $validator->validate($request->only(['judul']));
        $data = $this->repository->update($request->all(),$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function deleteDiagnosa($id){
        $data = $this->repository->deleteById($id);
        return $this->jsonSuccess('sukses',200,$data);
    }
}