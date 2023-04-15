<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UsersTransformer extends TransformerAbstract
{
    public function transform(User $data)
    {
        return [
            'id' => $data->id,
            'username' => $data->username,
            'name' => $data->firstname . ' ' . $data->lastname,
            'telp' => $data->telp,
            'role' => $data->role
        ];
    }
}
