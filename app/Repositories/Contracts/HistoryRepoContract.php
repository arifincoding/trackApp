<?php

namespace App\Repositories\Contracts;

interface HistoryRepoContract
{
    public function deleteByIdService(int $id): array;
}