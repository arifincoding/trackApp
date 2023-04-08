<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Repository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Repositories\Contracts\UserRepoContract;
use Illuminate\Database\Eloquent\Collection;

class UserRepository extends Repository implements UserRepoContract
{

    function __construct(User $model)
    {
        parent::__construct($model, 'user');
    }

    function getlistData(array $inputs = []): Collection
    {
        $data = $this->model->where('role', '!=', 'pemilik');
        isset($inputs['search']) ? $data->search($inputs['search']) : null;
        isset($inputs['role']) ? $data->where('role', $inputs['role']) : null;
        isset($inputs['limit']) ? $data->take($inputs['limit']) : null;
        return $data->get();
    }

    function getDataById(int $id): User
    {
        $data = $this->findById($id);
        return $data;
    }

    function findByUsername(string $username): User
    {
        $data = $this->model->where('username', $username)->first();
        return $data;
    }

    function changePassword(array $inputs, string $username): bool
    {
        $check = $this->model->where('username', $username)->firstOrFail();
        $attributs = [
            'password' => Hash::make($inputs['new_password']),
        ];
        $this->save($attributs, $check->id);
        return true;
    }

    public function registerUser(int $id): array
    {
        $date = Carbon::now('GMT+7');
        $password = Str::random(8);
        $attributs = [
            'username' => $date->format('y') . $date->format('m') . sprintf("%03d", $id),
            'password' => Hash::make($password)
        ];
        $data = $this->save($attributs, $id);
        return [
            'email' => $data->email,
            'username' => $data->username,
            'password' => $password
        ];
    }
}
