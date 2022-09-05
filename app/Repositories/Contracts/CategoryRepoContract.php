<?php

namespace App\Repositories\Contracts;

interface CategoryRepoContract {
    public function saveData(array $attributs, int $id):array;
    public function getListData(int $limit, string $search):array;
    public function getDataById(int $id):array;
    public function getDataNotInResponbility(string $username);
    public function deleteDataById(int $id):array;
}