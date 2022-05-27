<?php

namespace App\Validations;

use App\Validations\Validation;

class HistoryValidation extends Validation{

    public function __construct(){
        $this->rules = [
            'status'=>'required',
            'pesan'=>'required'
        ];
    }

}