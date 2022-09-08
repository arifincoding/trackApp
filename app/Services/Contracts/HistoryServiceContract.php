<?php

namespace App\Services\Contracts;

interface HistoryServiceContract
{
    public function newHistory(array $inputs, int $id): array;
}