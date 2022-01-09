<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Repository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Exception;
use App\Exceptions\Handler;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class userRepository extends Repository{

    function __construct(User $model){
        parent::__construct($model);
    }

    function create(array $input):array
    {
        $akun = $input['namaDepan'].Str::random(3);
        $attribut = [
            'firstName'=>$input['namaDepan'],
            'lastName'=>$input['namaBelakang'],
            'username'=>$akun,
            'password'=>Hash::make($akun),
            'gender'=>$input['jenisKelamin'],
            'phoneNumber'=>$input['noHp'],
            'address'=>$input['alamat'],
            'role'=>$input['peran'],
            'email'=>$input['email'],
            'status'=>'registered'
        ];
        $data = $this->save($attribut);
        return ['idPegawai'=>$data->id,'username'=>$data->username,'password'=>$data->username];
    }

    function update(array $input, string $id):array
    {
        $attribut=[
            'firstName'=>$input['namaDepan'],
            'lastName'=>$input['namaBelakang'],
            'gender'=>$input['jenisKelamin'],
            'phoneNumber'=>$input['noHp'],
            'address'=>$input['alamat'],
            'role'=>$input['peran'],
            'email'=>$input['email']
        ];
        $find = $this->findById($id);
        if($find->email !== $input['email'] && $find->status === 'registered'){
            $akun = $input['namaDepan'].Str::random(3);
            $attribut['username'] = $akun;
            $attribut['password'] = Hash::make($akun);
        }
        $data = $this->save($attribut, $id);
        return ['idPegawai'=>$id];
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
        throw new Exception('tidak bisa update status pegawai karena pegawai belum mengganti username dan password akunnya');
    }

    function getlistData(array $filters):array
    {
        $columns = ['id','username','firstName','lastName','role','status'];
        $limit = isset($filters['limit']) ? $filters['limit'] : 10;
        $status = isset($filters['status']) ? $filters['status'] : 'all';
        
        if($status == 'all'){
            $data = $this->getAll($limit,['firstName','asc'])->get($columns);
        }else{
            $data = $this->getAll($limit,['firstName','asc'])->where('status',$status)->get($columns);
        }
        
        $arrayData = [];
        foreach($data as $key => $item){
            $arrayData[$key]['idPegawai'] = $item->id;
            $arrayData[$key]['namaPengguna'] = $item->username;
            $arrayData[$key]['nama'] = $item->firstName.' '.$item->lastName;
            $arrayData[$key]['peran'] = $item->role;
            $arrayData[$key]['status'] = $item->status;
        }

        if($arrayData===[]){
            throw new ModelNotFoundException('not found');
        }
        return $arrayData;
    }

    function getDataById($id):array
    {
        $data = $this->findById($id);
        return[
            'idPegawai' => $data->id,
            'namaPengguna' => $data->username,
            'nama' => $data->firstName.' '.$data->lastName,
            'namaDepan' => $data->firstName,
            'namaBelakang' => $data->lastName,
            'peran' => $data->role,
            'status' => $data->status,
            'email' => $data->email,
            'noHp' => $data->phoneNumber,
            'alamat'=> $data->address
        ];
    }
}