<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ResponbilityService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Contracts\ResponbilityControllerContract;

class ResponbilityController extends Controller implements ResponbilityControllerContract
{

    private ResponbilityService $service;

    public function __construct(ResponbilityService $service)
    {
        $this->service = $service;
    }

    function all(string $id): JsonResponse
    {
        $data = $this->service->getAllRespobilities($id);
        return $this->jsonSuccess('sukses', 200, $data);
    }

    function create(Request $request, int $id): JsonResponse
    {
        $inputs = $request->only(['idKategori']);
        $data = $this->service->newResponbilities($inputs, $id);
        if ($data['success'] === false) {
            return $this->jsonValidationError($data['message']);
        }
        return $this->jsonMessageOnly($data['message']);
    }

    public function delete(int $id): JsonResponse
    {
        $data = $this->service->deleteResponbilityById($id);
        return $this->jsonMessageOnly($data);
    }
}