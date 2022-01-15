<?php

namespace App\Http\Controllers;

use App\Repositories\ResponbilityRepository;

class ResponbilityController extends Controller{
    
    public function __construct(ResponbilityRepository $repository){
        $this->repository = $repository;
    }

    public function delete($id){
        $data = $this->repository->deleteDataById($id);
        return $this->jsonSuccess('sukses hapus tanggung jawab',200, $data);
    }
}