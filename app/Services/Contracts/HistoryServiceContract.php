<?php

namespace App\Services\Contracts;
use App\Validations\HistoryValidation;

interface HistoryServiceContract {
    public function create(array $inputs, int $id, HistoryValidation $validator): array;
}