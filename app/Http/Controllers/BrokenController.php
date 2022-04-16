<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\BrokenRepository;
use App\Validations\BrokenValidation;
use App\Repositories\ServiceRepository;
use Illuminate\Http\JsonResponse;


class BrokenController extends Controller{

    public function __construct(BrokenRepository $broken, ServiceRepository $service){
        $this->brokenRepository = $broken;
        $this->serviceRepository = $service;
    }

    public function getListBrokenByIdService($id){
        $data = $this->brokenRepository->getListDataByIdService($id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function newBrokenByIdService(Request $request,$id,BrokenValidation $validator){
        $inputs = $request->only('judul','deskripsi');
        $validation = $validator->validate($inputs);
        $findService = $this->serviceRepository->getDataById($id);
        $data = $this->brokenRepository->create($inputs,$id, $findService['butuhKonfirmasi']);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function getBrokenById($id){
        $data = $this->brokenRepository->getDataById($id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function updateBroken(Request $request, $id, BrokenValidation $validator){
        $inputs = $request->only('judul','deskripsi');
        $validation = $validator->validate($inputs);
        $data = $this->brokenRepository->update($inputs,$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function updateBrokenCost(Request $request, $id, BrokenValidation $validator){
        $inputs = $request->only('biaya');
        $validator->cost();
        $validator->validate($inputs);
        $findBroken = $this->brokenRepository->getDataById($id);
        $findService = $this->serviceRepository->getDataById($findBroken['idService']);
        $totalCost = $this->setTotalCost($inputs['biaya'],$findBroken['biaya'],$findService['totalBiaya']);
        $this->serviceRepository->updateTotalPrice($findBroken['idService'],$totalCost);
        $data = $this->brokenRepository->update($inputs,$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function updateBrokenCofirmation(Request $request,$id, BrokenValidation $validator){
        $inputs = $request->only('dikonfirmasi');
        $validator->confirm();
        $validator->validate($inputs);
        $data = $this->brokenRepository->update($inputs,$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function deleteBroken($id){
        $data = $this->brokenRepository->deleteById($id);
        return $this->jsonMessageOnly('sukses hapus data kerusakan');
    }

    private function setTotalCost($cost, $brokenCost, $totalCost){
        $total = 0;
        if($brokenCost !== null){
            $total = $totalCost + ($cost - $brokenCost);
        }
        else if($totalCost !== null){
            $total = $totalCost + $cost;
        }else{
            $total = $cost;
        }
        return $total;
    }
}