<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;

class Service extends Model{
    protected $fillable = [
        'kode','keluhan','uangMuka','status','estimasiBiaya','idCustomer','idProduct','butuhPersetujuan','disetujui','konfirmasiBiaya','diambil','waktuMasuk','waktuAmbil','usernameCS','usernameTeknisi','totalBiaya','garansi'
    ];
    public $timestamps = false;

    public function customer(){
        return $this->belongsTo(Customer::class,'idCustomer');
    }

    public function product(){
        return $this->belongsTo(Product::class,'idProduct');
    }
}

?>