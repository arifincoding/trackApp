<?php

namespace App\Repositories\Contracts;

use App\Models\Broken;
use Illuminate\Database\Eloquent\Collection;

interface BrokenRepoContract
{
    public function getListDataByIdService(int $idService, array $filter = []): Collection;
    public function getDataById(int $id): array;
    public function findDataByIdService(int $id, string $filter): ?Broken;
    public function setCostInNotAgreeToZero(int $idService): bool;
    public function deleteByIdService(int $id): array;
}