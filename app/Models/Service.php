<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model{
    protected $fillable = [
        'nama','kategori','keluhan','cacatProduk','kelengkapan','catatan','uangMuka','status','estimasiBiaya','idCustomer','butuhKonfirmasi','dikonfirmasi','konfirmasiHarga','diambil','tanggalMasuk','tanggalAmbil','jamAmbil','jamMasuk','usernameCS','usernameTeknisi','totalBiaya'
    ];
}

?>