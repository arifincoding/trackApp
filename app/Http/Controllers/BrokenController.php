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
use App\Http\Controllers\Contracts\BrokenControllerContract;

class BrokenController extends Controller implements BrokenControllerContract {

    private $brokenRepository;
    private $serviceRepository;

    public function __construct(BrokenRepository $broken, ServiceRepository $service)
    {
        $this->brokenRepository = $broken;
        $this->serviceRepository = $service;
    }

    public function getListByIdService(int $id): JsonResponse
    {
        $query = $this->brokenRepository->getListDataByIdService($id);
        $fractal = new Manager();
        $data = $fractal->createData(new Collection($query,new BrokensTransformer))->toArray();
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function newByIdService(Request $request,int $id,BrokenValidation $validator): JsonResponse
    {
        $inputs = $request->only('judul','deskripsi');
        $validation = $validator->validate($inputs);
        $findService = $this->serviceRepository->findDataById($id);
        $data = $this->brokenRepository->create($inputs,$id, $findService->butuhPersetujuan);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function getBrokenById(int $id): JsonResponse
    {
        $data = $this->brokenRepository->getDataById($id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function update(Request $request, int $id, BrokenValidation $validator): JsonResponse
    {
        $inputs = $request->only('judul','deskripsi');
        $validator->validate($inputs);
        $data = $this->brokenRepository->update($inputs,$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function updateCost(Request $request, int $id, BrokenValidation $validator): JsonResponse
    {
        $inputs = $request->only('biaya');
        $validator->cost();
        $validator->validate($inputs);
        $data = $this->brokenRepository->update($inputs,$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function updateCofirmation(Request $request, int $id, BrokenValidation $validator): JsonResponse
    {
        $inputs = $request->only('disetujui');
        $validator->confirm();
        $validator->validate($inputs);
        $data = $this->brokenRepository->update($inputs,$id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    public function delete( int $id): JsonResponse
    {
        $data = $this->brokenRepository->deleteById($id);
        return $this->jsonMessageOnly('sukses hapus data kerusakan');
    }
}