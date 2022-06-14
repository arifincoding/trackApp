<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\BrokenRepository;
use App\Validations\BrokenValidation;
use App\Repositories\ServiceRepository;
use Illuminate\Http\JsonResponse;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use App\Transformers\BrokensTransformer;

class BrokenController extends Controller{

    private $brokenRepository;
    private $serviceRepository;

    public function __construct(BrokenRepository $broken, ServiceRepository $service){
        $this->brokenRepository = $broken;
        $this->serviceRepository = $service;
    }

    public function getListBrokenByIdService($id){
        $query = $this->brokenRepository->getListDataByIdService($id);
        $fractal = new Manager();
        $data = $fractal->createData(new Collection($query,new BrokensTransformer))->toArray();
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function newBrokenByIdService(Request $request,$id,BrokenValidation $validator){
        $inputs = $request->only('judul','deskripsi');
        $validation = $validator->validate($inputs);
        $findService = $this->serviceRepository->findDataById($id);
        $data = $this->brokenRepository->create($inputs,$id, $findService->butuhPersetujuan);
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
        $data = $this->brokenRepository->update($inputs,$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function updateBrokenCofirmation(Request $request,$id, BrokenValidation $validator){
        $inputs = $request->only('disetujui');
        $validator->confirm();
        $validator->validate($inputs);
        $data = $this->brokenRepository->update($inputs,$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function deleteBroken($id){
        $data = $this->brokenRepository->deleteById($id);
        return $this->jsonMessageOnly('sukses hapus data kerusakan');
    }
}