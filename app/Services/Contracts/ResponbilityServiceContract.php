<?php

namespace App\Services\Contracts;

interface ResponbilityServiceContract
{
    public function getAllRespobilities(string $username): array;
    public function newResponbilities(array $inputs, int $id): array;
    public function deleteResponbilityById(int $id): string;
}