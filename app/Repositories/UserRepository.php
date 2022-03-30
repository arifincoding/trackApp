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
            'role'=> $filters['peran'] ?? null
        ];
        $likeWhere=[];
        if(isset($filters['cari'])){
            $likeWhere=[
                'username'=>$filters['cari'],
                'firstName'=>$filters['cari'],
                'lastName'=>$filters['cari']
            ];
        }
        $data = $this->getWhere($limit,$where,[],$likeWhere);
        
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
        return [
            'idPegawai' => $data->id,
            'namaPengguna' => $data->username,
            'nama' => $data->firstName.' '.$data->lastName,
            'namaDepan' => $data->firstName,
            'namaBelakang' => $data->lastName,
            'namaPendek'=>$data->shortName,
            'jenisKelamin'=>$data->gender,
            'tanggalBergabung'=>$data->joiningDate,
            'peran' => $data->role,
            'status' => $data->status,
            'email' => $data->email,
            'noHp' => $data->phoneNumber,
            'alamat'=> $data->address
        ];
    }

    function getDataByUsername($username):array
    {
        $data = $this->model->where('username',$username)->firstOrFail();

        return [
            'namaPengguna'=>$data->username,
            'nama'=>$data->firstName.' '.$data->lastName,
            'namaPendek'=>$data->shortName,
            'noHp'=>$data->phoneNumber,
            'alamat'=>$data->address,
            'peran'=>$data->role
        ];
    }

    function create(array $input):array
    {
        $attribut = [
            'firstName'=>$input['namaDepan'],
            'lastName'=>$input['namaBelakang'],
            'shortName'=>$input['namaPendek'],
            'gender'=>$input['jenisKelamin'],
            'joiningDate'=>$input['tanggalBergabung'],
            'phoneNumber'=>$input['noHp'],
            'address'=>$input['alamat'],
            'role'=>$input['peran'],
            'email'=>$input['email'],
            'status'=>'registered'
        ];
        $data = $this->save($attribut);
        $register = $this->registerUser($data->joiningDate, $data->id);
        return [
            'idPegawai'=>$data->id, 
            'username'=>$register['username'],
            'password'=>$register['password'],
            'email'=>$data->email
        ];
    }

    function update(array $input, string $id):array
    {
        $attribut=[
            'firstName'=>$input['namaDepan'],
            'lastName'=>$input['namaBelakang'],
            'shortName'=>$input['namaPendek'],
            'gender'=>$input['jenisKelamin'],
            'joiningDate'=>$input['tanggalBergabung'],
            'phoneNumber'=>$input['noHp'],
            'address'=>$input['alamat'],
            'role'=>$input['peran'],
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

    private function registerUser(string $joiningDate, string $idUser){
        $date = DateAndTime::setDateFromString($joiningDate);
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