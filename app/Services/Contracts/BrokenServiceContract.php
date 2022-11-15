<?php

namespace App\Services\Contracts;

interface BrokenServiceContract
{
    public function getListBrokenByIdService(int $id): array;
    public function newBrokenByIdService(array $inputs, int $id): array;
    public function getBrokenById(int $id): array;
    public function updateBroken(array $inputs, int $id): array;
    public function updateBrokenCost(array $inputs, int $id): array;
    public function updateBrokenConfirmation(array $inputs, int $id): array;
    public function deleteBrokenById(int $id): string;
}