<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model{
    protected $fillable = [
        'idService','status','pesan','waktu'
    ];
    public $timestamps = false;
}