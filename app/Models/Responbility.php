<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Responbility extends Model{
    protected $fillable =['username','idKategori'];
    public $timestamps = false;
}