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
        if ($peran = $inputs['role'] ?? null) {
            $data->where('role', $peran);
        }
        if ($limit = $inputs['limit'] ?? null) {
            $data->take($limit);
        }
        return $data->get();
    }

    function getDataById(int $id): User
    {
        $attributs = ['id as employee_id', 'username', 'firstname', 'lastname', 'gender', 'telp', 'role', 'email', 'address'];
        $data = $this->findById($id, $attributs);
        return $data;
    }

    function findByUsername(string $username): User
    {
        $attributs = ['id', 'username', 'firstname', 'lastname', 'gender', 'telp', 'role', 'email', 'address'];
        $data = $this->model->select($attributs)->where('username', $username)->first();
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
