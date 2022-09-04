<?php

namespace App\Http\Controllers\Contracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Validations\ServiceValidation;

interface ServiceControllerContract{
    public function getListService(Request $request): JsonResponse;
    public function getServiceById(Request $request,$id): JsonResponse;
    public function getServiceQueue(Request $request,int $id): JsonResponse;
    public function getProgressService(Request $request,$id): JsonResponse;
    public function getServiceTrack(string $id): JsonResponse;
    public function newService(Request $request, ServiceValidation $validator):JsonResponse;
    public function updateService(Request $request, $id, ServiceValidation $validator): JsonResponse;
    public function updateServiceStatus(Request $request,$id, ServiceValidation $validator): JsonResponse;
    public function setServiceTake(string $id): JsonResponse;
    public function setConfirmCost(string $id): JsonResponse;
    public function updateWarranty(Request $request, $id,ServiceValidation $validator): JsonResponse;
    public function setConfirmation(Request $request,string $id,ServiceValidation $validator): JsonResponse;
    public function deleteService($id): JsonResponse;
}

?>