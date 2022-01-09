<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProccessTrack extends Model{
    protected $fillable = [
        'idDiagnosa','status','title','date','time'
    ];
}

?>