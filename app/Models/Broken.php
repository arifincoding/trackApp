<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Broken extends Model{
    protected $fillable = ['judul','dikonfirmasi','deskripsi','idService','biaya'];
}

?>