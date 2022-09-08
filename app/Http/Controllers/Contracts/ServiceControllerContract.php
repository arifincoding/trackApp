<?php

namespace App\Http\Controllers\Contracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface ServiceControllerContract
{
    public function getListService(Request $request): JsonResponse;
    public function getServiceById(Request $request, int $id): JsonResponse;
    public function getServiceQueue(Request $request, string $username): JsonResponse;
    public function getProgressService(Request $request, string $username): JsonResponse;
    public function getServiceTrack(string $code): JsonResponse;
    public function newService(Request $request): JsonResponse;
    public function updateService(Request $request, int $id): JsonResponse;
    public function updateServiceStatus(Request $request, int $id): JsonResponse;
    public function setServiceTake(int $id): JsonResponse;
    public function setConfirmCost(int $id): JsonResponse;
    public function updateWarranty(Request $request, int $id): JsonResponse;
    public function setConfirmation(Request $request, int $id): JsonResponse;
    public function deleteService(int $id): JsonResponse;
}