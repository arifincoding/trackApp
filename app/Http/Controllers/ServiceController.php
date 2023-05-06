<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\ServiceService;
use App\Http\Controllers\Contracts\ServiceControllerContract;

class ServiceController extends Controller implements ServiceControllerContract
{

    private ServiceService $service;

    function __construct(ServiceService $service)
    {
        $this->service = $service;
    }

    function getListService(Request $request): JsonResponse
    {
        $inputs = $request->only('status', 'category', 'search');
        $data = $this->service->getListService($inputs);
        return $this->jsonSuccess('sukses', 200, $data);
    }

    function getServiceById(Request $request, int $id): JsonResponse
    {
        $data = $this->service->getServiceById($id, $request->all());
        return $this->jsonSuccess('sukses', 200, $data);
    }

    function getServiceQueue(Request $request, string $id): JsonResponse
    {
        $inputs = $request->only('category', 'search');
        $data = $this->service->getServiceQueue($id, $inputs);
        return $this->jsonSuccess('sukses', 200, $data);
    }

    function getProgressService(Request $request, string $id): JsonResponse
    {
        $inputs = $request->only('status', 'category', 'search');
        $data = $this->service->getProgressService($id, $inputs);
        return $this->jsonSuccess('sukses', 200, $data);
    }

    public function getServiceTrack(string $id): JsonResponse
    {
        $data = $this->service->getServiceTrack($id);
        return $this->jsonSuccess($data['message'], 200, $data['data']);
    }

    private function inputService()
    {
        return [
            'complaint',
            'need_approval',
            'down_payment',
            'estimated_cost',
            'customer.name',
            'customer.telp',
            'customer.is_whatsapp',
            'product.name',
            'product.category_id',
            'product.completeness',
            'product.product_defects',
            'note',
        ];
    }

    function newService(Request $request): JsonResponse
    {
        $inputs = $request->only($this->inputService());
        $data = $this->service->newService($inputs);
        return $this->jsonSuccess('sukses', 200, $data);
    }

    public function updateService(Request $request, int $id): JsonResponse
    {
        $inputs = $request->only($this->inputService());
        $data = $this->service->updateServiceById($inputs, $id);
        return $this->jsonSuccess('sukses', 200, $data);
    }

    public function updateServiceStatus(Request $request, int $id): JsonResponse
    {
        $inputs = $request->only('status');
        $data = $this->service->updateServiceStatus($inputs, $id);
        return $this->jsonSuccess('sukses', 200, $data);
    }

    public function setServiceTake(int $id): JsonResponse
    {
        $data = $this->service->setServiceTake($id);
        return $this->jsonSuccess('sukses', 200, $data['data']);
    }

    public function setConfirmCost(int $id): JsonResponse
    {
        $data = $this->service->setServiceConfirmCost($id);
        return $this->jsonSuccess('sukses', 200, $data['data']);
    }

    public function updateWarranty(Request $request, int $id): JsonResponse
    {
        $inputs = $request->only('warranty');
        $data = $this->service->updateServiceWarranty($inputs, $id);
        return $this->jsonSuccess('sukses', 200, $data);
    }

    public function setConfirmation(Request $request, int $id): JsonResponse
    {
        $inputs =  $request->only('is_approved');
        $data = $this->service->setServiceConfirmation($inputs, $id);
        return $this->jsonSuccess('sukses', 200, $data['data']);
    }

    public function deleteService(int $id): JsonResponse
    {
        $data = $this->service->deleteServiceById($id);
        return $this->jsonMessageOnly($data);
    }
}
