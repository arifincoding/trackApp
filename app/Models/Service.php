<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model{
    protected $fillable = [
        'kode','keluhan','uangMuka','status','estimasiBiaya','idCustomer','idProduct','butuhPersetujuan','disetujui','konfirmasiBiaya','diambil','waktuMasuk','waktuAmbil','usernameCS','usernameTeknisi','totalBiaya','garansi'
    ];
    public $timestamps = false;

    public function klien(){
        return $this->belongsTo(Customer::class,'idCustomer');
    }

    public function produk(){
        return $this->belongsTo(Product::class,'idProduct');
    }

    public function kerusakan(){
        return $this->hasMany(Broken::class,'idService');
    }

    public function riwayat(){
        return $this->hasMany(History::class,'idService');
    }
}

?>