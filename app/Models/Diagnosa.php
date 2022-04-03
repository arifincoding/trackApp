<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnosa extends Model{
    protected $fillable = ['judul','dikonfirmasi','status','idService','biaya','idGaransi'];
}

?>