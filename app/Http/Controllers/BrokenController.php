<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\BrokenRepository;
use App\Validations\BrokenValidation;
use App\Repositories\ServiceRepository;
use Illuminate\Http\JsonResponse;


class BrokenController extends Controller{

    private $brokenRepository;
    private $serviceRepository;

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
        $data = $this->brokenRepository->create($inputs,$id, $findService['butuhPersetujuan']);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function getBrokenById($id){
        $data = $this->brokenRepository->getDataById($id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function updateBroken(Request $request, $id, BrokenValidation $validator){
        $inputs = $request->only('judul','deskripsi');
        $validator->validate($inputs);
        $data = $this->brokenRepository->update($inputs,$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function updateBrokenCost(Request $request, $id, BrokenValidation $validator){
        $inputs = $request->only('biaya');
        $validator->cost();
        $validator->validate($inputs);
        $totalCost = $this->setTotalCost($id,$inputs['biaya']);
        $data = $this->brokenRepository->update($inputs,$id);
        $this->serviceRepository->updateTotalPrice($data['idService'],$totalCost);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function updateBrokenCofirmation(Request $request,$id, BrokenValidation $validator){
        $inputs = $request->only('disetujui');
        $validator->confirm();
        $validator->validate($inputs);
        $total = $this->setTotalCostBrokenAgree($id,$inputs['disetujui']);
        $data = $this->brokenRepository->update($inputs,$id);
        if($total !== null){
            $this->serviceRepository->updateTotalPrice($data['idService'],$total);
        }
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function deleteBroken($id){
        $data = $this->brokenRepository->deleteById($id);
        return $this->jsonMessageOnly('sukses hapus data kerusakan');
    }

    private function setTotalCost(int $id, int $cost){
        $findBroken = $this->brokenRepository->getDataById($id);
        $findService = $this->serviceRepository->getDataById($findBroken['idService']);
        if($findBroken['biaya'] !== null){
            return $findService['totalBiaya'] + ($cost - $findBroken['biaya']);
        }
        else if($findService['totalBiaya'] !== null){
            return $findService['totalBiaya'] + $cost;
        }else{
            return $cost;
        }
    }

    private function setTotalCostBrokenAgree(int $id,bool $isAgree){
        $dataBroken = $this->brokenRepository->getDataById($id);
        if($dataBroken['disetujui'] !== $isAgree){
            $dataService = $this->serviceRepository->getDataById($dataBroken['idService']);
            if($isAgree === true && $dataBroken['disetujui'] !== null){
                return $dataService['totalBiaya'] + $dataBroken['biaya'];
            }
            else if($isAgree === false){
                return $dataService['totalBiaya'] - $dataBroken['biaya'];
            }
        }
        return null;
    }
}