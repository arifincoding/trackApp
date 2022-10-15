<?php

namespace App\Validations;

use App\Validations\Validation;

class CategoryValidation extends Validation
{

    function __construct()
    {
        $this->exceptionMessages = [
            'categories' => 'could not accessing all categories data caused the given data is invalid',
            'create' => 'could not create a single category data caused the given data is invalid',
            'update' => 'could not update a single category data caused the given data is invalid'
        ];
    }
    function query()
    {
        $this->rules = [
            'limit' => 'filled|numeric',
            'cari' => 'filled'
        ];
    }
    function post(string $id = null)
    {
        $this->rules = [
            'nama' => 'required|unique:categories,nama'
        ];
        if ($id !== null) {
            $this->rules = ['nama' => 'required|unique:categories,nama,' . $id];
        }
    }
}