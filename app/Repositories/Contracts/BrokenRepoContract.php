<?php

namespace App\Repositories\Contracts;

use App\Models\Broken;
use Illuminate\Database\Eloquent\Collection;

interface BrokenRepoContract
{
    public function getListDataByIdService(int $idService, array $filter = []): Collection;
    public function getDataById(int $id): Broken;
    public function findOneDataByWhere(array $filters): ?Broken;
    public function setCostInNotAgreeToZero(int $idService): bool;
    public function deleteByIdService(int $id): bool;
}