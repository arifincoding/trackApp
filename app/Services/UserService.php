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
use Illuminate\Support\Facades\Log;

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
        Log::info("user trying to login in the app", ["username" => $inputs["username"]]);
        $this->userValidator->login();
        $this->userValidator->validate($inputs);
        if (!$token = Auth::attempt($inputs)) {
            Log::warning("user login failed caused the password is false");
            return [
                'success' => false,
                'error' => [
                    'password' => [
                        'password salah'
                    ]
                ]
            ];
        }
        Log::info("user login successfully");
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
        Log::info("user trying to logging out to the app");
        Auth::logout();
        Log::info("user logging out successfully");
        return 'sukses logout';
    }

    public function getMyAccount(): array
    {
        Log::info("User is trying to accessing his account data");
        $data = $this->userRepository->findByUsername(Auth::payload()->get('username'));
        Log::info("user is accessing his account data");
        return $data;
    }

    public function updateMyAccount(array $inputs): string
    {
        Log::info("user is tring to update his account data", ["data" => $inputs]);
        $find = $this->userRepository->findByUsername(Auth::payload()->get('username'));
        Log::info("user data by his username found", ["username" => $find["username"]]);
        $this->userValidator->update($find['id']);
        $this->userValidator->validate($inputs);
        $this->userRepository->save($inputs, $find['id']);
        Log::info("user update his account data successfully");
        return 'sukses update akun';
    }

    public function changePassword(array $inputs): string
    {
        Log::info("user trying to change his account password");
        $this->userValidator->changePassword();
        $this->userValidator->validate($inputs);
        $this->userRepository->changePassword($inputs, Auth::payload()->get('username'));
        Log::info("user change his account password successfully");
        return 'sukses merubah sandi akun';
    }

    public function getListUser(array $inputs): array
    {
        Log::info("user is trying to accessing all user data", ["filters" => $inputs]);
        $this->userValidator->get();
        $this->userValidator->validate($inputs);
        $query = $this->userRepository->getListData($inputs);
        Log::info("user is accessing all user data");
        $fractal = new Manager();
        $data = $fractal->createData(new Collection($query, new UsersTransformer))->toArray();
        return $data;
    }

    public function getUserById(int $id): array
    {
        Log::info("user is trying to accessing a single user data by id user", ["id user" => $id]);
        $data = $this->userRepository->getDataById($id);
        Log::info("user is accessing a single user data by id user", ["id user" => $data["idPegawai"]]);
        return $data;
    }

    public function newUser(array $inputs): array
    {
        Log::info("user is trying to create a single user data", ["data" => $inputs]);
        $this->userValidator->post();
        $this->userValidator->validate($inputs);
        $data = $this->userRepository->save($inputs);
        Log::info("user create a single user data successfully", ["id user" => $data->id]);
        Log::info("trying to register account for this user", ["id user" => $data->id]);
        $register = $this->userRepository->registerUser($data->id);
        Log::info("registering account for this user successfully", ["username user" => $register['username']]);
        Log::info("trying to send username and password to this user email", ["username user" => $register['username']]);
        // Mail::to($register['email'])->send(new EmployeeMail($register['username'], $register['password']));
        Log::info("sending username and password to this user email successfully");
        return ['idPegawai' => $data->id];
    }

    public function updateUserById(array $inputs, int $id): array
    {
        Log::info("user is trying to update a single user data by id user", ["id user" => $id]);
        $this->userValidator->post($id);
        $this->userValidator->validate($inputs);
        $data = $this->userRepository->save($inputs, $id);
        Log::info("user update a single user by id successfully", ["id user" => $data->id]);
        if ($inputs['peran'] !== 'teknisi') {
            Log::info("trying to delete respobility data by username caused role this user is not tecnicion", ["username" => $data->user, "id user" => $id]);
            $this->responbilityRepository->deleteByUsername($data->username);
            Log::info("deleting responbility data by username successfully", ["username" => $data->username]);
        }
        return ['idPegawai' => $data->id];
    }

    public function deleteUserById(int $id): string
    {
        Log::info("user trying to delete a single user data by id user", ["id user" => $id]);
        $find = $this->userRepository->getDataById($id);
        $delete = $this->userRepository->delete($id);
        Log::info("user delete a single user data by id user successfully", ["id user" => $id]);
        if ($delete === true) {
            Log::info("trying to delete responbility data by username", ["username" => $find['username'], "id user" => $id]);
            $this->responbilityRepository->deleteByUsername($find['username']);
            Log::info("deleting responbility data by username successfully", ["username" => $find['username']]);
        }
        return 'sukses hapus data pegawai';
    }
}