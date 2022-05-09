<?php

namespace App\Validations;

use App\Validations\Validation;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserValidation extends Validation{
    
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