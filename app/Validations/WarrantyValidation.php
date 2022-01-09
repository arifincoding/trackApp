<?php

namespace App\Validations;
use App\Validations\Validation;

class WarrantyValidation extends Validation{
    function __construct(){
        $this->rules = [
            'keluhan'=>'required',
            'customerService'=>'required'
        ];
    }
}

?>