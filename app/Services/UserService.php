<?php

namespace App\Services;

use App\Services\Contracts\UserServiceContract;
use App\Repositories\UserRepository;
use App\Validations\UserValidation;
use Illuminate\Support\Facades\Auth;
use App\Repositories\ResponbilityRepository;
use App\Mails\EmployeeMail;
use Illuminate\Support\Facades\Mail;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use App\Transformers\UsersTransformer;

class UserService implements UserServiceContract
{
    private $userRepository;
    private $responbilityRepository;
    private $userValidator;

    public function __construct(UserRepository $user, ResponbilityRepository $responbility, UserValidation $validator)
    {
        $this->userRepository = $user;
        $this->responbilityRepository = $responbility;
        $this->userValidator = $validator;
    }

    public function login(array $inputs): array
    {
        $this->userValidator->login();
        $this->userValidator->validate($inputs);
        if (!$token = Auth::attempt($inputs)) {
            return [
                'success' => false,
                'error' => [
                    'password' => [
                        'password salah'
                    ]
                ]
            ];
        }
        return [
            'success' => true,
            'token' => $token
        ];
    }

    public function createRefreshToken(): string
    {
        return Auth::refresh();
    }

    public function logout(): string
    {
        Auth::logout();
        return 'sukses logout';
    }

    public function getMyAccount(): array
    {
        $data = $this->userRepository->findByUsername(Auth::payload()->get('username'));
        return $data;
    }

    public function updateMyAccount(array $inputs): string
    {
        $find = $this->userRepository->findByUsername(Auth::payload()->get('username'));
        $this->userValidator->update($find['id']);
        $this->userValidator->validate($inputs);
        $this->userRepository->save($inputs, $find['id']);
        return 'sukses update akun';
    }

    public function changePassword(array $inputs): string
    {
        $this->userValidator->changePassword();
        $this->userValidator->validate($inputs);
        $this->userRepository->changePassword($inputs, Auth::payload()->get('username'));
        return 'sukses merubah sandi akun';
    }

    public function getListUser(array $inputs): array
    {
        $this->userValidator->get();
        $this->userValidator->validate($inputs);
        $query = $this->userRepository->getListData($inputs);
        $fractal = new Manager();
        $data = $fractal->createData(new Collection($query, new UsersTransformer))->toArray();
        return $data;
    }

    public function getUserById(int $id): array
    {
        $data = $this->userRepository->getDataById($id);
        return $data;
    }

    public function newUser(array $inputs): array
    {
        $this->userValidator->post();
        $this->userValidator->validate($inputs);
        $data = $this->userRepository->save($inputs);
        $register = $this->userRepository->registerUser($data->id);
        // Mail::to($register['email'])->send(new EmployeeMail($register['username'], $register['password']));
        return ['idPegawai' => $data->id];
    }

    public function updateUserById(array $inputs, int $id): array
    {
        $this->userValidator->post($id);
        $this->userValidator->validate($inputs);
        $data = $this->userRepository->save($inputs, $id);
        if ($inputs['peran'] !== 'teknisi') {
            $this->responbilityRepository->deleteByUsername($data->username);
        }
        return ['idPegawai' => $data->id];
    }

    public function deleteUserById(int $id): string
    {
        $find = $this->userRepository->getDataById($id);
        $delete = $this->userRepository->deleteById($id);
        if ($delete === true) {
            $this->responbilityRepository->deleteByUsername($find['username']);
        }
        return 'sukses hapus data pegawai';
    }
}