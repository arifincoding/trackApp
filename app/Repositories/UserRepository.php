<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Repository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class userRepository extends Repository{

    function __construct(User $model){
        parent::__construct($model);
    }

    function getlistData(array $inputs)
    {
        $filters = [
            'limit'=>$inputs['limit'] ?? 0,
            'where'=>[
                'peran'=> $inputs['peran'] ?? null
            ]
        ];
        $data = $this->getWhere(['*'],$filters,false);
        $data->where('peran','!=','pemilik');
        return $data->get();
    }

    function getDataById($id):array
    {
        $attributs = ['id as idPegawai','username','namaDepan','namaBelakang','jenisKelamin','noHp','peran','email','alamat'];
        $data = $this->findById($id,$attributs);
        return $data->toArray();
    }

    function findByUsername(string $username):array
    {
        $attributs = ['id','username','namaDepan','namaBelakang','jenisKelamin','noHp','peran','email','alamat'];
        $data = $this->model->select($attributs)->where('username',$username)->first();
        return $data->toArray();
    }

    function create(array $attributs):array
    {
        $data = $this->save($attributs);
        return [
            'idPegawai'=>$data->id
        ];
    }

    function update(array $attributs, string $id):array
    {
        $data = $this->save($attributs, $id);
        return [
            'idPegawai'=>$data->id,
            'username'=>$data->username
        ];
    }

    function deleteById(string $id):bool
    {
        $data = $this->delete($id);
        return $data;
    }

    function changePassword(array $inputs, string $username):bool
    {
        $check = $this->model->where('username',$username)->firstOrFail();
        $attributs = [
            'password'=>Hash::make($inputs['sandiBaru']),
        ];
        $data = $this->save($attributs,$check->id);
        return true;
    }

    public function registerUser(int $idUser):array
    {
        $date = Carbon::now('GMT+7');
        $password = Str::random(8);
        $attributs=[
            'username'=>$date->format('y').$date->format('m').sprintf("%03d",$idUser),
            'password'=> Hash::make($password)
        ];
        $data = $this->save($attributs,$idUser);
        return [
            'email'=> $data->email,
            'username'=> $data->username,
            'password'=>$password
        ];
    }
}