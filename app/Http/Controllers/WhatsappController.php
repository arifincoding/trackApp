<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WhatsappService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Contracts\WhatsappControllerContract;

class WhatsappController extends Controller implements WhatsappControllerContract
{

    private $service;

    public function __construct(WhatsappService $service)
    {
        $this->service = $service;
    }

    public function scan(): JsonResponse
    {
        $data = $this->service->scanQr();
        return $this->jsonSuccess('sukses', 200, ['qr' => $data]);
    }

    public function chat(Request $request, $id): JsonResponse
    {
        $data = $this->service->sendMessage($request->all(), $id);
        return $this->jsonMessageOnly($data);
    }
}