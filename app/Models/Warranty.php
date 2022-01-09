<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warranty extends Model{
    protected $fillable = ['idService','completeness','complaint','productDefects','entryDate','entryTime','pickDate','pickTime','note','csName','technicianName'];
}