<?php

namespace App\Repositories\Contracts;

interface HistoryRepoContract {
    public function create(array $input, int $id): array;
    public function deleteByIdService(int $id): array;
}