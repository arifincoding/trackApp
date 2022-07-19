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
            'butuhPersetujuan'=>'required|boolean',
            'estimasiBiaya'=>'nullable|numeric',
            'uangMuka'=>'nullable|numeric'
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
            'disetujui'=>'required|boolean'
        ];
    }
}