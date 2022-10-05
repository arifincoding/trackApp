<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Repository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Repositories\Contracts\UserRepoContract;
use Illuminate\Database\Eloquent\Collection;

class userRepository extends Repository implements UserRepoContract
{

    function __construct(User $model)
    {
        parent::__construct($model);
    }

    function getlistData(array $inputs): Collection
    {
        $filters = [
            'limit' => $inputs['limit'] ?? 0,
            'where' => [
                'peran' => $inputs['peran'] ?? null
            ]
        ];
        $data = $this->getWhere(['*'], $filters, false);
        $data->where('peran', '!=', 'pemilik');
        return $data->get();
    }

    function getDataById(int $id): array
    {
        $attributs = ['id as idPegawai', 'username', 'namaDepan', 'namaBelakang', 'jenisKelamin', 'noHp', 'peran', 'email', 'alamat'];
        $data = $this->findById($id, $attributs);
        return $data->toArray();
    }

    function findByUsername(string $username): array
    {
        $attributs = ['id', 'username', 'namaDepan', 'namaBelakang', 'jenisKelamin', 'noHp', 'peran', 'email', 'alamat'];
        $data = $this->model->select($attributs)->where('username', $username)->first();
        return $data->toArray();
    }

    function changePassword(array $inputs, string $username): bool
    {
        $check = $this->model->where('username', $username)->firstOrFail();
        $attributs = [
            'password' => Hash::make($inputs['sandiBaru']),
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