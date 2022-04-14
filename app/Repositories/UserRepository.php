<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Repository;
use App\Repositories\ResponbilityRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Exception;
use App\Exceptions\Handler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Helpers\DateAndTime;
use Illuminate\Support\Facades\DB;

class userRepository extends Repository{

    function __construct(User $model, ResponbilityRepository $responbility){
        parent::__construct($model);
        $this->responbility = $responbility;
    }

    function getlistData(array $inputs):array
    {
        $filters = [
            'limit'=>$inputs['limit'] ?? 0,
            'where'=>[
                'peran'=> $inputs['peran'] ?? null
            ]
        ];
        $cari = $inputs['cari'] ?? null;
        if($cari){
            if(count(explode(' ',$cari)) < 2){
            $filters['likeWhere']=[
                'username'=>$cari,
                'namaDepan'=>$cari,
                'namaBelakang'=>$cari
            ];
            }
        }
        
        $attributs = ['id as idPegawai','username',DB::raw("CONCAT(namaDepan,' ',namaBelakang) AS nama"),'noHp','peran'];
        
        $data = $this->getWhere($attributs,$filters,false);
        
        if(count(explode(' ',$cari)) > 1){
            $data->where(DB::raw('CONCAT_WS(" ",namaDepan,namaBelakang)'),'LIKE','%'.$cari.'%');
        }
        
        return $data->get()->toArray();
    }

    function getDataById($id):array
    {
        $attributs = ['id as idPegawai','username','namaDepan','namaBelakang','jenisKelamin','noHp','peran','email','alamat'];
        $data = $this->findById($id,$attributs);
        return $data->toArray();
    }

    function create(array $attributs):array
    {
        $data = $this->save($attributs);
        $register = $this->registerUser($data->id);
        return [
            'idPegawai'=>$data->id,
            'email'=>$data->email,
            'username'=>$register['username'],
            'password'=>$register['password']
        ];
    }

    function update(array $attributs, string $id):array
    {
        $find = $this->findById($id);
        $returnData = ['idPegawai'=>$find->id];
        if($find->email !== $attributs['email']){
            $akun = Str::random(8);
            $attributs['password'] = Hash::make($akun);
            $returnData += [
                'email'=>$attributs['email'],
                'username'=>$find->username,
                'password'=>$akun
            ];
        }
        $data = $this->save($attributs, $id);
        return $returnData;
    }

    function deleteById(string $id){
        $data = $this->delete($id);
        return ['sukses'=>$data];
    }

    function changePassword(array $inputs, string $username){
        $check = $this->model->where('username',$username)->firstOrFail();
        if(Hash::check($inputs['sandiLama'],$check->password)){
            $attributs = [
                'password'=>Hash::make($inputs['sandiBaru']),
            ];
            $data = $this->save($attributs,$check->id);
        }
        return ['sukses'=>true];
    }

    private function registerUser(string $idUser){
        $date = DateAndTime::getDateNow(false);
        $password = Str::random(8);
        $attributs=[
            'username'=>$date->format('y').$date->format('m').sprintf("%03d",$idUser),
            'password'=> Hash::make($password)
        ];
        $data = $this->save($attributs,$idUser);
        return [
            'username'=> $data->username,
            'password'=>$password
        ];
    }
}