<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model{
    protected $fillable = [
        'nama','kategori','keluhan','cacatProduk','kelengkapan','catatan','uangMuka','status','estimasiBiaya','idCustomer','butuhKonfirmasi','dikonfirmasi','konfirmasiBiaya','diambil','tanggalMasuk','tanggalAmbil','jamAmbil','jamMasuk','usernameCS','usernameTeknisi','totalBiaya'
    ];
}

?>