<?php

namespace App\Validations;
use App\Validations\Validation;

class ServiceValidation extends Validation{
    function __construct(){
        
    }

    function post(){
        $this->rules = [
            'namaCustomer'=>'required|regex:/^[\pL\s\-]+$/u',
            'noHp'=>'numeric',
            'bisaWhatsapp'=>'in:true,false',
            'namaBarang'=>'required',
            'kategori'=>'required|exists:categories,title',
            'keluhan'=>'required',
            'butuhKonfirmasi'=>'required|in:true,false'
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
}