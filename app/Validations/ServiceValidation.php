<?php

namespace App\Validations;
use App\Validations\Validation;

class ServiceValidation extends Validation{
    
    function __construct(){
        $this->rules = [
            'namaCustomer'=>'required|regex:/^[\pL\s\-]+$/u',
            'noHp'=>'numeric',
            'bisaWA'=>'boolean',
            'namaProduk'=>'required',
            'kategori'=>'required|exists:categories,nama',
            'keluhan'=>'required',
            'butuhKonfirmasi'=>'required|boolean'
        ];
    }

    function statusService(){
        $this->rules = [
            'status'=>'required'
        ];
    }

    function serviceWarranty(){
        $this->rules = [
            'garansi'=>'required'
        ];
    }

    function confirmation(){
        $this->rules = [
            'dikonfirmasi'=>'required|boolean'
        ];
    }
}