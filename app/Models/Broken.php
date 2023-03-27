<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Broken extends Model
{

    use HasFactory;

    protected $fillable = ['title', 'is_approved', 'description', 'service_id', 'cost'];
    public $timestamps = false;
}
