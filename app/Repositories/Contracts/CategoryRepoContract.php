<?php

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepoContract
{
    public function getListData(int $limit = 0, string $search = ''): Collection;
    public function getDataById(int $id): Category;
    public function getDataNotInResponbility(string $username): Collection;
}