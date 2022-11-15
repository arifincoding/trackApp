<?php

namespace App\Services\Contracts;

interface ServiceServiceContract
{
    public function getListService(array $inputs): array;
    public function getServiceById(int $id, array $inputs): array;
    public function getServiceQueue(string $username, array $inputs): array;
    public function getProgressService(string $username, array $inputs): array;
    public function getServiceTrack(string $code): array;
    public function newService(array $inputs): array;
    public function updateServiceById(array $inputs, int $id): array;
    public function updateServiceStatus(array $inputs, int $id): array;
    public function setServiceTake(int $id): array;
    public function setServiceConfirmCost(int $id): array;
    public function updateServiceWarranty(array $inputs, int $id): array;
    public function setServiceConfirmation(array $inputs, int $id): array;
    public function deleteServiceById(int $id): string;
}