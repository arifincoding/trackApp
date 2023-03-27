<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{

    use HasFactory;

    protected $fillable = [
        'code', 'complaint', 'down_payment', 'status', 'estimated_cost', 'customer_id', 'product_id', 'need_approval', 'is_approved', 'is_cost_confirmation', 'is_take', 'entry_at', 'taked_at', 'cs_username', 'tecnician_username', 'total_cost', 'warranty'
    ];
    public $timestamps = false;

    public function klien()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function produk()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function kerusakan()
    {
        return $this->hasMany(Broken::class, 'service_id');
    }

    public function riwayat()
    {
        return $this->hasMany(History::class, 'service_id');
    }
}
