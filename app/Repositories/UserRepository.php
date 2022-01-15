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
        $columns = ['id','username','firstName','lastName','role','status'];
        $limit = $filters['limit'] ?? 10;
        $status = $filters['status'] ?? 'all';
        
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
        $dataResponbility = $this->responbility->getListDataByIdUser($data->username);
        $returnData = [
            'idPegawai' => $data->id,
            'namaPengguna' => $data->username,
            'nama' => $data->firstName.' '.$data->lastName,
            'namaDepan' => $data->firstName,
            'namaBelakang' => $data->lastName,
            'namaPendek'=>$data->shortName,
            'peran' => $data->role,
            'status' => $data->status,
            'email' => $data->email,
            'noHp' => $data->phoneNumber,
            'alamat'=> $data->address,
            'tanggungJawab'=>null
        ];
        if($dataResponbility !== false){
            $returnData['tanggungJawab'] = $dataResponbility;
        }

        return $returnData;
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
        throw new Exception('tidak bisa update status pegawai karena pegawai belum mengganti password akunnya');
    }

    function newTechnicianResponbilities(array $inputs ,string $id){
        $check = $this->model->where('id',$id)->firstOrFail();
        if($check->role !== 'teknisi'){
            throw new Exception('gagal tambah tanggung jawab karena pegawai ini bukan teknisi');
        }
        $data = $this->responbility->create(['username'=>$check->username,'idKategori'=>$inputs['idKategori']]);
        return $data;
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