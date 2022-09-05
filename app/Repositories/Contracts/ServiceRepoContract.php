<?php

namespace App\Repositories\Contracts;

interface ServiceRepoContract {
    public function getListData(array $inputs);
    public function getDataWithRelationById(int $id);
    public function getListDataQueue($responbility, array $inputs);
    public function getListDataMyProgress(string $username,array $inputs);
    public function getDataByCode(string $code);
    public function create(array $attributs):array;
    public function setCodeService(int $id):void;
    public function update(array $attributs,int $id):array;
    public function setDataTake(int $id):array;
    public function deleteById(int $id):array;
}