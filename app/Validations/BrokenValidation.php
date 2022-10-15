<?php

namespace App\Validations;

use App\Validations\Validation;

class BrokenValidation extends Validation
{
    function __construct()
    {
        $this->rules = [
            'judul' => 'required',
            'deskripsi' => 'required'
        ];
        $this->exceptionMessages = [
            'create' => 'could not create a single broken data caused the given data is invalid',
            'update' => 'could not update a single broken data caused the given data is invalid',
            'updateCost' => 'could not update a single broken data caused the given data is invalid',
            'updateConfirm' => 'could not update broken confirmation caused the given data is invalid'
        ];
    }
    function confirm()
    {
        $this->rules = [
            'disetujui' => 'required|boolean'
        ];
    }
    function cost()
    {
        $this->rules = [
            'biaya' => 'required|numeric'
        ];
    }
}