<?php

namespace App\Validations;
use App\Validations\Validation;

class EmployeeValidation extends Validation{
    function __construct(){
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
}

?>