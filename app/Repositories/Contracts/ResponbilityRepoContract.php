<?php

namespace App\Repositories\Contracts;

interface ResponbilityRepoContract {
    public function getListDataByUsername(string $username);
    public function create(array $inputs, string $role, string $username):bool;
    public function deleteDataById(int $id):array;
    public function deleteByUsername(string $username):array;
}