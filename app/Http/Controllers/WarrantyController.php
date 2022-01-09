<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Repositories\WarrantyRepository;

class WarrantyController extends Controller{

    function __construct(WarrantyRepository $repository){
        $this->repository = $repository;
    }

    function newWarrantyDiagnosa(Request $request, $id){
        
    }
}