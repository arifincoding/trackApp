<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ResponbilityRepository;
use App\Repositories\UserRepository;
use App\Validations\ResponbilityValidation;
use Illuminate\Http\JsonResponse;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use App\Transformers\ResponbilitiesTransformer;

class ResponbilityController extends Controller{
    
    private $responbilityRepository;
    private $userRepository;

    public function __construct(ResponbilityRepository $responbility, UserRepository $user)
    {
        $this->responbilityRepository = $responbility;
        $this->userRepository = $user;
    }

    function getTechnicianResponbilities(string $id): JsonResponse
    {
        $query = $this->responbilityRepository->getListDataByUsername($id);
        if($query){
            $fractal = new Manager();
            $data = $fractal->createData(new Collection($query, new ResponbilitiesTransformer))->toArray();
            return $this->jsonSuccess('sukses',200,$data);
        }
        return $this->jsonSuccess('sukses',200,[]);
    }

    function newTechnicianResponbilities(Request $request, $id, ResponbilityValidation $validator): JsonResponse
    {
        $input = $request->only(['idKategori']);
        $validator->post($id,$input);
        $validation = $validator->validate($input);
        $findUser = $this->userRepository->getDataById($id);
        $data = $this->responbilityRepository->create($input, $findUser['peran'],$findUser['username']);
        return $this->jsonMessageOnly('sukses tambah tanggung jawab');
    }

    public function delete($id): JsonResponse
    {
        $data = $this->responbilityRepository->deleteDataById($id);
        return $this->jsonMessageOnly('sukses hapus data tanggung jawab');
    }
}