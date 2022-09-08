<?php

namespace App\Services\Contracts;

use App\Validations\ResponbilityValidation;

interface ResponbilityServiceContract
{
    function getAllRespobilities(string $username): array;
    function create(array $inputs, int $id, ResponbilityValidation $validator): array;
    public function delete(int $id): string;
}