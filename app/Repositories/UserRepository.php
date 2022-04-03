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

class userRepository extends Repository{

    function __construct(User $model, ResponbilityRepository $responbility){
        parent::__construct($model);
        $this->responbility = $responbility;
    }

    function getlistData(array $filters):array
    {
        $limit = $filters['limit'] ?? 0;
        $where=[
            'status'=> $filters['status'] ?? null,
            'peran'=> $filters['peran'] ?? null
        ];
        $likeWhere=[];
        if(isset($filters['cari'])){
            $likeWhere=[
                'username'=>$filters['cari'],
                'namaDepan'=>$filters['cari'],
                'namaBelakang'=>$filters['cari']
            ];
        }
        $attributs = ['id as idPegawai','username','namaDepan','namaBelakang','noHp','peran','status'];
        $data = $this->getWhere($attributs,$limit,$where,[],$likeWhere);
        return $data->toArray();
    }

    function getDataById($id):array
    {
        $attributs = ['id as idPegawai','username','namaDepan','namaBelakang','jenisKelamin','noHp','peran','status','email','alamat'];
        $data = $this->findById($id,$attributs);
        return $data->toArray();
    }

    function create(array $input):array
    {
        $attribut = [
            'namaDepan'=>$input['namaDepan'],
            'namaBelakang'=>$input['namaBelakang'],
            'jenisKelamin'=>$input['jenisKelamin'],
            'noHp'=>$input['noHp'],
            'alamat'=>$input['alamat'],
            'peran'=>$input['peran'],
            'email'=>$input['email'],
            'status'=>'registered'
        ];
        $data = $this->save($attribut);
        $register = $this->registerUser($data->id);
        return [
            'idPegawai'=>$data->id
        ];
    }

    function update(array $input, string $id):array
    {
        $attribut=[
            'namaDepan'=>$input['namaDepan'],
            'namaBelakang'=>$input['namaBelakang'],
            'jenisKelamin'=>$input['jenisKelamin'],
            'noHp'=>$input['noHp'],
            'alamat'=>$input['alamat'],
            'peran'=>$input['peran'],
            'email'=>$input['email']
        ];
        $find = $this->findById($id);
        if($find->email !== $input['email'] && $find->status === 'registered'){
            $akun = Str::random(8);
            $attribut['password'] = Hash::make($akun);
        }
        $data = $this->save($attribut, $id);
        return ['idPegawai'=>$id];
    }

    function deleteById(string $id){
        $data = $this->delete($id);
        return ['sukses'=>$data];
    }

    function changeStatus(string $status, $id):array
    {
        $check = $this->findById($id);
        if($check->status !== 'registered'){
            $attribut = [
                'status'=>$status
            ];
            $data = $this->save($attribut,$id);
            return ['idPegawai'=>$id];
        }
        throw new Exception('tidak bisa update status pegawai karena pegawai belum mengganti password akunnya');
    }

    function changePassword(array $inputs, string $username){
        $check = $this->model->where('username',$username)->firstOrFail();
        if(Hash::check($inputs['sandiLama'],$check->password)){
            $attributs = [
                'password'=>Hash::make($inputs['sandiBaru']),
            ];
            if($check->status === 'registered'){
                $attributs['status'] = 'active';
            }
            $data = $this->save($attributs,$check->id);
        }
        return ['sukses'=>true];
    }

    private function registerUser(string $idUser){
        $date = DateAndTime::getDateNow($isFormat=false);
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