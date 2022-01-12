<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\UserRepository;

class UserController extends Controller{
    function __construct(UserRepository $repository){
        $this->repository = $repository;
    }

    function getUserByUsername($username){
        $data = $this->repository->getDataByUsername($username);
        return $this->jsonSuccess('sukses',200,$data);
    }
}

?>