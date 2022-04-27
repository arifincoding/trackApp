<?php

namespace App\Validations;

use App\Validations\Validation;

class AuthValidation extends Validation{
    function __construct(){
        $this->rules = [
            'username'=>'required|exists:users,username',
            'password'=>'required'
        ];
    }
}