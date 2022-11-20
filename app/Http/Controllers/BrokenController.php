<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BrokenService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Contracts\BrokenControllerContract;

class BrokenController extends Controller implements BrokenControllerContract
{

    protected BrokenService $brokenService;

    public function __construct(BrokenService $broken)
    {
        $this->brokenService = $broken;
    }

    public function getListByIdService(int $id): JsonResponse
    {
        $data = $this->brokenService->getListBrokenByIdService($id);
        return $this->jsonSuccess('sukses', 200, $data);
    }

    public function newByIdService(Request $request, int $id): JsonResponse
    {
        $inputs = $request->only('judul', 'deskripsi');
        $data = $this->brokenService->newBrokenByIdService($inputs, $id);
        return $this->jsonSuccess('sukses', 200, $data);
    }

    public function getBrokenById(int $id): JsonResponse
    {
        $data = $this->brokenService->getBrokenById($id);
        return $this->jsonSuccess('sukses', 200, $data);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $inputs = $request->only('judul', 'deskripsi');
        $data = $this->brokenService->updateBroken($inputs, $id);
        return $this->jsonSuccess('sukses', 200, $data);
    }

    public function updateCost(Request $request, int $id): JsonResponse
    {
        $inputs = $request->only('biaya');
        $data = $this->brokenService->updateBrokenCost($inputs, $id);
        return $this->jsonSuccess('sukses', 200, $data);
    }

    public function updateCofirmation(Request $request, int $id): JsonResponse
    {
        $inputs = $request->only('disetujui');
        $data = $this->brokenService->updateBrokenConfirmation($inputs, $id);
        return $this->jsonSuccess('sukses', 200, $data);
    }

    public function delete(int $id): JsonResponse
    {
        $data = $this->brokenService->deleteBrokenById($id);
        return $this->jsonMessageOnly($data);
    }
}