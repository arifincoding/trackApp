<?php

namespace App\Repositories\Contracts;

interface ProductRepoContract {
    public function create(array $attributs):int;
    public function update(array $attributs, int $id):array;
    public function deleteById(int $id):array;
}

?>