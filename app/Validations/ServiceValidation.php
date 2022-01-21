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
            'mendukungWhatsapp'=>'in:true,false',
            'namaBarang'=>'required',
            'kategori'=>'required|exists:categories,title',
            'keluhan'=>'required',
            'membutuhkanSpesialis'=>'required|in:true,false',
            'membutuhkanKonfirmasi'=>'required|in:true,false'
        ];
    }
}