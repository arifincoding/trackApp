<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Broken extends Model
{

    use HasFactory;

    protected $fillable = ['judul', 'disetujui', 'deskripsi', 'idService', 'biaya'];
    public $timestamps = false;
}