<?php

namespace App\Validations;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Validation {
    
    protected array $rules = [];

    protected array $messages = [
        'required'=> ':attribute tidak boleh kosong',
        'unique'=> ':attribute ini sudah digunakan',
        'numeric'=>'nilai dari :attribute harus berupa angka',
        'filled'=> ':attribute tidak boleh kosong',
        'alpha'=> ':attribute harus berupa karakter alfabet',
        'in'=> 'nilai dari :attribute tidak diizinkan',
        'regex'=> ':attribute hanya mengizinkan karakter alfabet dan spasi',
        'between'=> 'jumlah karakter :attribute minimal :min , maksimal :max'
    ];

    protected array $attributes = [];

    public function validate(array $input=[]){
        $validator = Validator::make($input,$this->rules,$this->messages,$this->attributes);
        if($validator->fails()){
            throw new ValidationException($validator);
        }
        return true;
    }

}

?>