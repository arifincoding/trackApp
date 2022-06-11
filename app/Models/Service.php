<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model{
    protected $fillable = [
        'kode','keluhan','uangMuka','status','estimasiBiaya','idCustomer','idProduct','butuhPersetujuan','disetujui','konfirmasiBiaya','diambil','waktuMasuk','waktuAmbil','usernameCS','usernameTeknisi','totalBiaya','garansi'
    ];
}

?>