<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnosa extends Model{
    protected $fillable = ['title','confirmed','status','idService','price','idWarranty'];
}

?>