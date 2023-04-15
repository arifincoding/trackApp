<?php

namespace App\Validations;

use App\Validations\Validation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserValidation extends Validation
{

    public function __construct()
    {
        $this->exceptionMessages = [
            'login' => 'could not login in the app caused the given data is invalid',
            'updateAccount' => 'could not update account caused the given data is invalid',
            'changePassword' => 'could not change password account caused the given data is invalid',
            'allUser' => 'could not accessing all user data caused the given data is invalid',
            'create' => 'could not create a single user data caused the given data is invalid',
            'update' => 'could not update a single user data caused the given data is invalid'
        ];
    }

    public function login()
    {
        $this->rules = [
            'username' => 'required|exists:users,username',
            'password' => 'required'
        ];
    }

    public function get()
    {
        $this->rules = [
            'limit' => 'filled|numeric'
        ];
    }

    function post($id = null)
    {
        $this->rules = [
            'firstname' => 'required|regex:/^[\pL\s\-]+$/u',
            'lastname' => 'required|regex:/^[\pL\s\-]+$/u',
            'gender' => 'required|regex:/^[\pL\s\-]+$/u',
            'telp' => 'required|numeric',
            'email' => 'required|unique:users|email',
            'role' => 'required'
        ];
        if ($id !== null) {
            $this->rules['email'] = 'required|email|unique:users,email,' . $id;
        }
    }

    public function update(int $id)
    {
        $this->rules = [
            'telp' => 'required|numeric',
            'email' => 'required|email|unique:users,email,' . $id,
            'address' => 'required'
        ];
    }

    public function changePassword()
    {
        $data = User::where('username', Auth::payload()->get('username'))->first();
        $this->rules = [
            'old_password' => [
                'required',
                function ($attribute, $value, $fail) use ($data) {
                    if (!(Hash::check($value, $data->password))) {
                        $fail('sandi lama salah');
                    }
                }
            ],
            'new_password' => 'required|min:8'
        ];
    }
}
