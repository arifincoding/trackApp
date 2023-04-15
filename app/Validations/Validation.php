<?php

namespace App\Validations;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Validation
{

    protected array $rules = [];

    protected array $messages = [
        'required' => ':attribute tidak boleh kosong',
        'unique' => ':attribute ini sudah digunakan',
        'numeric' => ':attribute harus berupa angka',
        'filled' => ':attribute tidak boleh kosong',
        'alpha' => ':attribute harus berupa karakter alfabet',
        'in' => 'nilai dari :attribute tidak diizinkan',
        'regex' => ':attribute tidak benar',
        'between' => 'jumlah karakter :attribute minimal :min , maksimal :max',
        'exists' => ':attribute tidak tersedia',
        'array' => ':Attribute harus berupa array',
        'email' => 'alamat email tidak valid'
    ];

    protected array $exceptionMessages = [];

    protected array $attributes = [];

    public function setExceptionMessage(string $messages)
    {
        $this->exceptionMessages = $messages;
    }

    public function validate(array $input = [], string $action)
    {
        $validator = Validator::make($input, $this->rules, $this->messages, $this->attributes);
        if ($validator->fails()) {
            $exceptionMessage = $this->exceptionMessages[$action] ?? 'the given data is invalid';
            $exception = new ValidationException($validator);
            Log::warning($exceptionMessage, ["errors" => $exception->errors()]);
            throw $exception;
        }
        return true;
    }
}
