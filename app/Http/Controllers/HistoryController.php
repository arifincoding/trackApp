<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\HistoryService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Contracts\HistoryControllerContract;

class HistoryController extends Controller implements HistoryControllerContract
{

    private $service;

    public function __construct(HistoryService $service)
    {
        $this->service = $service;
    }

    public function create(Request $request, int $id): JsonResponse
    {
        $inputs = $request->only(['status', 'pesan']);
        $data = $this->service->newHistory($inputs, $id);
        return $this->jsonSuccess('sukses', 200, $data);
    }
}