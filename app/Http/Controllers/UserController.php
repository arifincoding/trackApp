<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Contracts\UserControllerContract;

class UserController extends Controller implements UserControllerContract
{

    private $service;

    function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('username', 'password');
        $data = $this->service->login($credentials);
        if ($data['success'] === false) {
            return $this->jsonValidationError($data['error']);
        }
        return $this->jsonToken($data['token']);
    }

    public function createRefreshToken(): JsonResponse
    {
        $newToken = $this->service->createRefreshToken();
        return $this->jsonToken($newToken);
    }

    public function logout(): JsonResponse
    {
        $data = $this->service->logout();
        return $this->jsonMessageOnly($data);
    }

    function getMyAccount(): JsonResponse
    {
        $data = $this->service->getMyAccount();
        return $this->jsonSuccess('sukses ambil data', 200, $data);
    }

    function updateMyAccount(Request $request): JsonResponse
    {
        $inputs = $request->only(['email', 'noHp', 'alamat']);
        $data = $this->service->updateMyAccount($inputs);
        return $this->jsonMessageOnly($data);
    }

    function changePassword(Request $request): JsonResponse
    {
        $inputs = $request->only(['sandiLama', 'sandiBaru']);
        $data = $this->service->changePassword($inputs);
        return $this->jsonMessageOnly($data);
    }

    function all(Request $request): JsonResponse
    {
        $inputs = $request->only(['limit', 'peran']);
        $data = $this->service->getListUser($inputs);
        return $this->jsonSuccess('sukses', 200, $data);
    }

    function show(int $id): JsonResponse
    {
        $data = $this->service->getUserById($id);
        return $this->jsonSuccess('sukses', 200, $data);
    }

    function create(Request $request): JsonResponse
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
        $data = $this->service->newUser($inputs);
        return $this->jsonSuccess('sukses', 200, $data);
    }

    function update(Request $request, int $id): JsonResponse
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
        $data = $this->service->updateUserById($inputs, $id);
        return $this->jsonSuccess('sukses', 200, $data);
    }

    function delete(int $id): JsonResponse
    {
        $data = $this->service->deleteUserById($id);
        return $this->jsonMessageOnly($data);
    }
}