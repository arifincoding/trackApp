<?php

namespace App\Validations;
use App\Validations\Validation;

class BrokenValidation extends Validation{
    function __construct(){
        $this->rules = [
            'judul'=>'required',
            'deskripsi'=>'required'
        ];
    }
    function confirm(){
        $this->rules = [
            'disetujui'=>'required|boolean'
        ];
    }
    function cost(){
        $this->rules = [
            'biaya'=>'required|numeric'
        ];
    }
}

?>