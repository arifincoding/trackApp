<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UsersTransformer extends TransformerAbstract{
    public function transform(User $data){
        return [
            'idPegawai'=>$data->id,
            'username'=>$data->username,
            'nama'=>$data->namaDepan.' '.$data->namaBelakang,
            'noHp'=>$data->noHp,
            'peran'=>$data->peran
        ];
    }
}