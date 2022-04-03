<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceTrack extends Model{
    protected $fillable = [
        'idService','status','judul','tanggal','jam','idGaransi'
    ];
}