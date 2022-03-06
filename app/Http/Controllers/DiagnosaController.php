<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\DiagnosaRepository;
use App\Validations\DiagnosaValidation;
use App\Repositories\ServiceRepository;
use Illuminate\Http\JsonResponse;


class DiagnosaController extends Controller{

    public function __construct(DiagnosaRepository $diagnosa, ServiceRepository $service){
        $this->diagnosaRepository = $diagnosa;
        $this->serviceRepository = $service;
    }

    public function getListDiagnosaByIdService($id){
        $data = $this->diagnosaRepository->getListDataByIdService($id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function newDiagnosaByIdService(Request $request,$id,DiagnosaValidation $validator){
        $validation = $validator->validate($request->only(['judul']));
        
        $findService = $this->serviceRepository->findDataById($id);

        $data = $this->diagnosaRepository->create($request->all(),$id, $findService['confirmed']);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function getDiagnosaById($id){
        $data = $this->diagnosaRepository->getDataById($id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function updateDiagnosa(Request $request, $id, DiagnosaValidation $validator){
        $validation = $validator->validate($request->only(['judul']));
        $data = $this->diagnosaRepository->update($request->all(),$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function updateDiagnosaStatus(Request $request, $id){
        $data = $this->diagnosaRepository->updateStatus($request->all(),$id);
        return $this->jsonSUccess('sukses',200,$data);
    }

    public function updateDiagnosaCost(Request $request, $id){
        $findDiagnosa = $this->diagnosaRepository->findDataById($id);
        $findService = $this->serviceRepository->findDataById($findDiagnosa['idService']);
        $price = $request->input('biaya');
        $totalPrice = 0;
        if($findDiagnosa['harga'] !== null){
            $totalPrice = $findService['totalPrice'] + ($price - $findDiagnosa['harga']);
        }
        else if($findService['totalPrice'] !== null){
            $totalPrice = $findService['totalPrice'] + $price;
        }else{
            $totalPrice = $price;
        }
        $this->serviceRepository->updateTotalPrice($id,$totalPrice);
        $data = $this->diagnosaRepository->updateCost($request->all(),$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function deleteDiagnosa($id){
        $data = $this->diagnosaRepository->deleteById($id);
        return $this->jsonSuccess('sukses',200,$data);
    }
}