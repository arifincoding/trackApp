<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model{
    protected $fillable = [
        'nama','noHp','bisaWA'
    ];
    public $timestamps = false;
}

?>