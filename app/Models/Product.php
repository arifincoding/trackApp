<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Product extends Model{
    protected $fillable=['nama','kategori','cacatProduk','kelengkapan','catatan'];
    public $timestamps = false;
}