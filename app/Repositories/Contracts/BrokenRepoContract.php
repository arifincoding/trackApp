<?php

namespace App\Repositories\Contracts;

interface BrokenRepoContract {
    public function getListDataByIdService(int $idService, array $filter);
    public function getDataById(int $id):array;
    public function findDataByIdService(int $id, string $filter);
    public function create(array $attributs,int $idService, int $confirmed=0):array;
    public function update(array $attributs, int $id):array;
    public function setCostInNotAgreeToZero(int $idService):bool;
    public function deleteById(int $id):array;
    public function deleteByIdService(int $id):array;
}

?>