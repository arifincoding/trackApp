<?php

namespace App\Validations;

use App\Validations\Validation;

class CategoryValidation extends Validation{

    function query(){
        $this->rules = [
            'limit'=>'filled|numeric',
            'cari'=>'filled'
        ];
    }
    function post(string $id = null){
        $this->rules= [
            'kategori'=>'required|unique:categories,title'];
        if($id !== null){
            $this->rules = [
                'kategori'=>'required|unique:categories,title,'.$id
            ];
        }
    }
}

?>