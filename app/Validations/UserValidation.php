<?php

namespace App\Validations;

use App\Validations\Validation;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserValidation extends Validation{
    
    public function login(){
        $this->rules = [
            'username'=>'required|exists:users,username',
            'password'=>'required'
        ];
    }

    public function get(){
        $this->rules = [
            'limit'=> 'filled|numeric'
        ];
    }

    function post($id = null){
        $this->rules = [
            'namaDepan'=> 'required|regex:/^[\pL\s\-]+$/u',
            'namaBelakang'=> 'required|regex:/^[\pL\s\-]+$/u',
            'jenisKelamin'=> 'required|regex:/^[\pL\s\-]+$/u',
            'noHp'=> 'required|numeric',
            'email'=> 'required|unique:users|email',
            'peran' => 'required'
        ];
        if($id !== null){
            $this->rules['email'] = 'required|email|unique:users,email,'.$id;
        }
    }

    public function update(int $id){
        $this->rules = [
            'noHp'=>'required|numeric',
            'email'=>'required|email|unique:users,email,'.$id,
            'alamat'=>'required'
        ];
    }

    public function changePassword(){
        $data = User::where('username',auth()->payload()->get('username'))->first();
        $this->rules = [
            'sandiLama'=>[
                'required',
                function($attribute,$value,$fail) use ($data){
                    if(!(Hash::check($value,$data->password))){
                        $fail('sandi lama salah');
                    }
                }
            ],
            'sandiBaru'=>'required|min:8'
        ];
    }
}

?>