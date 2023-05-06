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
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UserService implements UserServiceContract
{
    private UserRepository $userRepository;
    private ResponbilityRepository $responbilityRepository;
    private UserValidation $validator;

    public function __construct(UserRepository $user, ResponbilityRepository $responbility, UserValidation $validator)
    {
        $this->userRepository = $user;
        $this->responbilityRepository = $responbility;
        $this->validator = $validator;
    }

    public function login(array $inputs): string
    {
        $this->validator->login();
        $this->validator->validate($inputs, 'login');
        if (!$token = Auth::attempt($inputs)) {
            throw ValidationException::withMessages(['password' => 'password yang anda masukkan salah']);
        }
        return $token;
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
        return $data->toArray();
    }

    public function updateMyAccount(array $inputs): string
    {
        $find = $this->userRepository->findByUsername(Auth::payload()->get('username'));
        $this->validator->update($find->id);
        $this->validator->validate($inputs, 'updateAccount');
        $this->userRepository->save($inputs, $find->id);
        return 'sukses update akun';
    }

    public function changePassword(array $inputs): string
    {
        $this->validator->changePassword();
        $this->validator->validate($inputs, 'changePassword');
        $this->userRepository->changePassword($inputs, Auth::payload()->get('username'));
        return 'sukses merubah sandi akun';
    }

    public function getListUser(array $inputs): array
    {
        $this->validator->get();
        $this->validator->validate($inputs, 'allUser');
        $query = $this->userRepository->getListData($inputs);
        $fractal = new Manager();
        $data = $fractal->createData(new Collection($query, new UsersTransformer))->toArray();
        return $data;
    }

    public function getUserById(int $id): array
    {
        $data = $this->userRepository->getDataById($id);
        return $data->toArray();
    }

    public function newUser(array $inputs): array
    {
        $this->validator->post();
        $this->validator->validate($inputs, 'create');
        $data = DB::transaction(function () use ($inputs) {
            $data = $this->userRepository->save($inputs);
            $register = $this->userRepository->registerUser($data->id);
            // Mail::to($register['email'])->send(new EmployeeMail($register['username'], $register['password']));
            return $data;
        });
        return ['user_id' => $data->id];
    }

    public function updateUserById(array $inputs, int $id): array
    {
        $this->validator->post($id);
        $this->validator->validate($inputs, 'update');
        $data = DB::transaction(function () use ($inputs, $id) {
            $data = $this->userRepository->save($inputs, $id);
            $inputs['role'] !== 'teknisi' ? $this->responbilityRepository->deleteByUsername($data->username) : null;
            return $data;
        });
        return ['user_id' => $data->id];
    }

    public function deleteUserById(int $id): string
    {
        DB::transaction(function () use ($id) {
            $find = $this->userRepository->findById($id);
            $this->userRepository->delete($id);
            $this->responbilityRepository->deleteByUsername($find->username);
        });
        return 'sukses hapus data pegawai';
    }
}
