<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\ResponbilityRepository;
use App\Validations\EmployeeValidation;
use Illuminate\Http\JsonResponse;
use App\Mails\EmployeeMail;
use Illuminate\Support\Facades\Mail;

class EmployeeController extends Controller{

    private $userRepository;
    private $responbilityRepository;

    function __construct(UserRepository $user, ResponbilityRepository $responbility){
        $this->userRepository = $user;
        $this->responbilityRepository = $responbility;
    }

    function getListEmployee(Request $request, EmployeeValidation $validator): JsonResponse
    {
        $filters = $request->only(['limit','peran','cari']);
        $validation = $validator->validate($filters);
        $data = $this->userRepository->getListData($filters);
        return $this->jsonSuccess('sukses',200,$data);
    }

    function getEmployeeById($id): JsonResponse
    {
        $dataUser = $this->userRepository->getDataById($id);
        return $this->jsonSuccess('sukses',200,$dataUser);
    }

    function createEmployee(Request $request, EmployeeValidation $validator): JsonResponse
    {
        $inputs = $request->only(['namaDepan','namaBelakang','jenisKelamin','noHp','alamat','peran','email']);
        $validator->post();
        $validation = $validator->validate($inputs);
        $data = $this->userRepository->create($inputs);
        Mail::to($data['email'])->send(new EmployeeMail($data['username'],$data['password']));
        return $this->jsonSuccess('sukses',200,['idPegawai'=>$data['idPegawai']]);
    }

    function updateEmployee(Request $request, $id, EmployeeValidation $validator): JsonResponse
    {
        $inputs = $request->only(['namaDepan','namaBelakang','jenisKelamin','noHp','alamat','peran','email']);
        $validator->post($id);
        $validation = $validator->validate($inputs);
        $data = $this->userRepository->update($inputs, $id);
        return $this->jsonSuccess('sukses',200,['idPegawai'=>$data['idPegawai']]);
    }

    function deleteEmployee($id){
        $find = $this->userRepository->getDataById($id);
        $data = $this->userRepository->deleteById($id);
        if($data['sukses'] === true){
            $this->responbilityRepository->deleteByUsername($find['username']);
        }
        return $this->jsonMessageOnly('sukses hapus data pegawai');
    }
}