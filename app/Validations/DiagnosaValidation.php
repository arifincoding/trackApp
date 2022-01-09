<?php

namespace App\Validations;
use App\Validations\Validation;

class DiagnosaValidation extends Validation{
    function __construct(){
        $this->rules = [
            'judul'=>'required'
        ];
    }
}

?>