<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Responbility extends Model{

    protected $table = 'responbilities';
    protected $fillable =['username','idKategori'];
}