<?php

namespace App\Repositories\Contracts;

use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;

interface ServiceRepoContract
{
    public function getListData(array $inputs): Collection;
    public function getDataWithRelationById(int $id): ?Service;
    public function getListDataQueue($responbility, array $inputs): Collection;
    public function getListDataMyProgress(string $username, array $inputs): Collection;
    public function getDataByCode(string $code): ?Service;
    public function create(array $attributs): int;
    public function setCodeService(int $id): void;
    public function update(array $attributs, int $id): int;
    public function setDataTake(int $id): int;
}