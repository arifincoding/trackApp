<?php

namespace App\Validations;

use App\Validations\Validation;
use Illuminate\Validation\Rule;
use App\Models\User;

class ResponbilityValidation extends Validation{
    function post(string $id, array $input){
        $data = User::where('id',$id)->firstOrFail();
        $this->rules =[
            'idKategori'=>'required|array',
    ];
    foreach($input['idKategori'] as $key=>$item){
        $this->rules['idKategori.'.$key] = [
            'filled',
            'exists:categories,id',
            Rule::unique('responbilities','idKategori')->where(function ($q) use($data){
                return $q->where('username',$data->username);
            }),
            function($attribute,$value,$fail) use($input,$key){
            foreach($input['idKategori'] as $i=>$ktgr){
                if($key !== $i){
                    if($value == $ktgr){
                        $fail($attribute.' tidak boleh sama');
                    }
                }
            }
        }];
    }
    $this->attributes = [
        'idKategori'=>'kategori',
        'idKategori.*'=>'kategori'
    ];
    }
}