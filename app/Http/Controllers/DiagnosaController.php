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
        $inputs = $request->only('judul');
        $validation = $validator->validate($inputs);
        $findService = $this->serviceRepository->findDataById($id);
        $data = $this->diagnosaRepository->create($inputs,$id, $findService['butuhKonfirmasi']);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function getDiagnosaById($id){
        $data = $this->diagnosaRepository->getDataById($id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function updateDiagnosa(Request $request, $id, DiagnosaValidation $validator){
        $inputs = $request->only('judul');
        $validation = $validator->validate($inputs);
        $data = $this->diagnosaRepository->update($inputs,$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function updateDiagnosaStatus(Request $request, $id){
        $inputs = $request->only('status');
        $data = $this->diagnosaRepository->update($inputs,$id);
        return $this->jsonSUccess('sukses',200,$data);
    }

    public function updateDiagnosaCost(Request $request, $id){
        $inputs = $request->only('biaya');
        $findDiagnosa = $this->diagnosaRepository->findDataById($id);
        $findService = $this->serviceRepository->findDataById($findDiagnosa['idService']);
        $price = $request->input('biaya');
        $totalCost = $this->setTotalCost($inputs['biaya'],$findDiagnosa['biaya'],$findService['totalBiaya']);
        $this->serviceRepository->updateTotalPrice($id,$totalCost);
        $data = $this->diagnosaRepository->update($inputs,$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function deleteDiagnosa($id){
        $data = $this->diagnosaRepository->deleteById($id);
        return $this->jsonMessageOnly('sukses hapus data diagnosa');
    }

    private function setTotalCost($cost, $diagnosaCost, $totalCost){
        $total = 0;
        if($diagnosaCost !== null){
            $total = $totalCost + ($cost - $diagnosaCost);
        }
        else if($totalCost !== null){
            $total = $totalCost + $cost;
        }else{
            $total = $cost;
        }
        return $total;
    }
}