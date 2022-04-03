<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warranty extends Model{
    protected $fillable = ['idService','kelengkapan','keluhan','cacatProduk','tanggalMasuk','jamMasuk','tanggalAmbil','jamAmbil','catatan','usernameCS','usernameTeknisi'];
}