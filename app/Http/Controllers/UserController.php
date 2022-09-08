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
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use App\Transformers\UsersTransformer;
use App\Http\Controllers\Contracts\UserControllerContract;

class UserController extends Controller implements UserControllerContract
{

    private $repository;
    private $responbilityRepository;

    function __construct(UserRepository $repository, ResponbilityRepository $responbility)
    {
        $this->repository = $repository;
        $this->responbilityRepository = $responbility;
    }

    public function login(Request $request, UserValidation $validator): JsonResponse
    {
        $credentials = $request->only('username', 'password');
        $validator->login();
        $validator->validate($credentials);
        if (!$token = Auth::attempt($credentials)) {
            return $this->jsonValidationError([
                'password' => [
                    'password salah'
                ]
            ]);
        }
        return $this->jsonToken($token);
    }

    public function createRefreshToken(): JsonResponse
    {
        $newToken = Auth::refresh();
        return $this->jsonToken($newToken);
    }

    public function logout(): JsonResponse
    {
        Auth::logout();
        return $this->jsonMessageOnly('sukses logout');
    }

    function getMyAccount(): JsonResponse
    {
        $data = $this->repository->findByUsername(Auth::payload()->get('username'));
        return $this->jsonSuccess('sukses ambil data', 200, $data);
    }

    function updateMyAccount(Request $request, UserValidation $validator): JsonResponse
    {
        $input = $request->only(['email', 'noHp', 'alamat']);
        $find = $this->repository->findByUsername(Auth::payload()->get('username'));
        $validator->update($find['id']);
        $validator->validate($input);
        $data = $this->repository->update($input, $find['id']);
        return $this->jsonMessageOnly('sukses update akun');
    }

    function changePassword(Request $request, UserValidation $validator): JsonResponse
    {
        $input = $request->only(['sandiLama', 'sandiBaru']);
        $validator->changePassword();
        $validator->validate($input);
        $data = $this->repository->changePassword($input, Auth::payload()->get('username'));
        return $this->jsonMessageOnly('sukses merubah sandi akun');
    }

    function all(Request $request, UserValidation $validator): JsonResponse
    {
        $filters = $request->only(['limit', 'peran']);
        $validator->get();
        $validator->validate($filters);
        $query = $this->repository->getListData($filters);
        $fractal = new Manager();
        $data = $fractal->createData(new Collection($query, new UsersTransformer))->toArray();
        return $this->jsonSuccess('sukses', 200, $data);
    }

    function show(int $id): JsonResponse
    {
        $dataUser = $this->repository->getDataById($id);
        return $this->jsonSuccess('sukses', 200, $dataUser);
    }

    function create(Request $request, UserValidation $validator): JsonResponse
    {
        $attributs = [
            'namaDepan',
            'namaBelakang',
            'jenisKelamin',
            'noHp',
            'alamat',
            'peran',
            'email'
        ];
        $inputs = $request->only($attributs);
        $validator->post();
        $validation = $validator->validate($inputs);
        $data = $this->repository->create($inputs);
        $register = $this->repository->registerUser($data['idPegawai']);
        Mail::to($register['email'])->send(new EmployeeMail($register['username'], $register['password']));
        return $this->jsonSuccess('sukses', 200, $data);
    }

    function update(Request $request, int $id, UserValidation $validator): JsonResponse
    {
        $attributs = [
            'namaDepan',
            'namaBelakang',
            'jenisKelamin',
            'noHp',
            'alamat',
            'peran',
            'email'
        ];
        $inputs = $request->only($attributs);
        $validator->post($id);
        $validation = $validator->validate($inputs);
        $data = $this->repository->update($inputs, $id);
        if ($inputs['peran'] !== 'teknisi') {
            $this->responbilityRepository->deleteByUsername($data['username']);
        }
        unset($data['username']);
        return $this->jsonSuccess('sukses', 200, $data);
    }

    function delete(int $id): JsonResponse
    {
        $find = $this->repository->getDataById($id);
        $delete = $this->repository->deleteById($id);
        if ($delete === true) {
            $this->responbilityRepository->deleteByUsername($find['username']);
        }
        return $this->jsonMessageOnly('sukses hapus data pegawai');
    }
}