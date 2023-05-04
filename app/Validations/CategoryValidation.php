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
            'search' => 'filled'
        ];
    }
    function post(string $id = null)
    {
        $this->rules = [
            'name' => 'required|unique:categories,name'
        ];
        if ($id !== null) {
            $this->rules = ['name' => 'required|unique:categories,name,' . $id];
        }
    }
}
