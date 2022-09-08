<?php

namespace App\Services\Contracts;

use App\Validations\ServiceValidation;

interface ServiceServiceContract
{
    public function getListService(array $inputs): array;
    public function getServiceById(array $inputs, int $id): array;
    public function getServiceQueue(array $inputs, string $username): array;
    public function getProgressService(array $inputs, string $username): array;
    public function getServiceTrack(string $code): array;
    public function newService(array $inputs, ServiceValidation $validator): array;
    public function updateService(array $request, int $id, ServiceValidation $validator): array;
    public function updateServiceStatus(array $request, int $id, ServiceValidation $validator): array;
    public function setServiceTake(int $id): array;
    public function setConfirmCost(int $id): array;
    public function updateWarranty(array $inputs, int $id, ServiceValidation $validator): array;
    public function setConfirmation(array $inputs, int $id, ServiceValidation $validator): array;
    public function deleteService(int $id): array;
}