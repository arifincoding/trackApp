<?php

namespace App\Validations;
use App\Validations\Validation;

class CustomerValidation extends Validation{
    function post(){
        $this->rules = [
            'nama'=>'required|regex:/^[\pL\s\-]+$/u',
            'noHp'=>'numeric',
            'mendukungWhatsapp'=>'required|in:true,false'
        ];
    }
}

?>