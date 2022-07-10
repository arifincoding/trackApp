<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Validations\UserValidation;
use Illuminate\Support\Facades\Auth;
use App\Repositories\ResponbilityRepository;
use Illuminate\Http\JsonResponse;
use App\Mails\EmployeeMail;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller{

    private $repository;
    private $responbilityRepository;

    function __construct(UserRepository $repository, ResponbilityRepository $responbility){
        $this->repository = $repository;
        $this->responbilityRepository = $responbility;
    }

    public function login(Request $request, UserValidation $validator){
        $credentials = $request->only('username','password');
        $validator->login();
        $validator->validate($credentials);
        if (!$token = auth()->attempt($credentials)){
            return $this->jsonValidationError([
                'password'=>[
                    'password salah'
                ]
                ]);
        }
        return $this->jsonToken($token);
    }

    public function createRefreshToken(){
        $newToken = auth()->refresh();
        return $this->jsonToken($newToken);
    }

    public function logout(){
        auth()->logout();
        return $this->jsonMessageOnly('sukses logout');
    }

    function getMyAccount(){
        $data = $this->repository->findByUsername(auth()->payload()->get('username'));
        return $this->jsonSuccess('sukses ambil data',200,$data);
    }

    function updateMyAccount(Request $request, UserValidation $validator){
        $input = $request->only(['email','noHp','alamat']);
        $find = $this->repository->findByUsername(auth()->payload()->get('username'));
        $validator->update($find['id']);
        $validator->validate($input);
        $data = $this->repository->update($input,$find['id']);
        return $this->jsonMessageOnly('sukses update akun');
    }

    function changeMyPassword(Request $request, UserValidation $validator){
        $input = $request->only(['sandiLama','sandiBaru']);
        $validator->changePassword();
        $validator->validate($input);
        $data = $this->repository->changePassword($input,auth()->payload()->get('username'));
        return $this->jsonMessageOnly('sukses merubah sandi akun');
    }

    function all(Request $request, UserValidation $validator): JsonResponse
    {
        $filters = $request->only(['limit','peran','cari']);
        $validator->get();
        $validator->validate($filters);
        $data = $this->repository->getListData($filters);
        return $this->jsonSuccess('sukses',200,$data);
    }

    function show($id): JsonResponse
    {
        $dataUser = $this->repository->getDataById($id);
        return $this->jsonSuccess('sukses',200,$dataUser);
    }

    function create(Request $request, UserValidation $validator): JsonResponse
    {
        $inputs = $request->only(['namaDepan','namaBelakang','jenisKelamin','noHp','alamat','peran','email']);
        $validator->post();
        $validation = $validator->validate($inputs);
        $data = $this->repository->create($inputs);
        $register = $this->repository->registerUser($data['idPegawai']);
        // Mail::to($register['email'])->send(new EmployeeMail($register['username'],$register['password']));
        return $this->jsonSuccess('sukses',200,$data);
    }

    function update(Request $request, $id, UserValidation $validator): JsonResponse
    {
        $inputs = $request->only(['namaDepan','namaBelakang','jenisKelamin','noHp','alamat','peran','email']);
        $validator->post($id);
        $validation = $validator->validate($inputs);
        $data = $this->repository->update($inputs, $id);
        return $this->jsonSuccess('sukses',200,$data);
    }

    function delete($id){
        $find = $this->repository->getDataById($id);
        $delete = $this->repository->deleteById($id);
        if($delete === true){
            $this->responbilityRepository->deleteByUsername($find['username']);
        }
        return $this->jsonMessageOnly('sukses hapus data pegawai');
    }
}

?>