<?php

namespace App\Validations;

use App\Validations\Validation;

class ServiceValidation extends Validation
{

    function __construct()
    {
        $this->rules = [
            'namaCustomer' => 'required|regex:/^[\pL\s\-]+$/u',
            'noHp' => 'nullable|numeric',
            'bisaWA' => 'boolean',
            'namaProduk' => 'required',
            'kategori' => 'required|exists:categories,nama',
            'keluhan' => 'required',
            'butuhPersetujuan' => 'required|boolean',
            'estimasiBiaya' => 'nullable|numeric',
            'uangMuka' => 'nullable|numeric'
        ];
        $this->exceptionMessages = [
            'create' => 'could not create a single service data caused the given data is invalid',
            'update' => 'could not update a single service data caused the given data is invalid',
            'updateStatus' => 'could not update service status caused the given data is invalid',
            'updateConfirmation' => 'could not set service confirmation caused the given data is invalid',
            'updateWarranty' => 'could not update service warranty caused the given data is invalid'
        ];
    }

    function statusService()
    {
        $this->rules = [
            'status' => 'required'
        ];
    }

    function serviceWarranty()
    {
        $this->rules = [
            'garansi' => 'required'
        ];
    }

    function confirmation()
    {
        $this->rules = [
            'disetujui' => 'required|boolean'
        ];
    }
}