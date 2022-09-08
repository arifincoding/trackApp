<?php

namespace App\Services\Contracts;

use App\Validations\BrokenValidation;

interface BrokenServiceContract
{
    public function getListByIdService(int $id): array;
    public function newByIdService(array $inputs, int $id, BrokenValidation $validator): array;
    public function getBrokenById(int $id): array;
    public function update(array $inputs, int $id, BrokenValidation $validator): array;
    public function updateCost(array $inputs, int $id, BrokenValidation $validator): array;
    public function updateCofirmation(array $inputs, int $id, BrokenValidation $validator): array;
    public function delete(int $id): string;
}