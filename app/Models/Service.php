<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Laravel\Scout\Attributes\SearchUsingFullText;
use Laravel\Scout\Attributes\SearchUsingPrefix;

class Service extends Model
{

    use HasFactory, Searchable;

    // use Searchable {
    // Searchable::search as parentSearch;
    // }

    protected $fillable = [
        'code', 'complaint', 'down_payment', 'status', 'estimated_cost', 'customer_id', 'product_id', 'need_approval', 'is_approved', 'is_cost_confirmation', 'is_take', 'entry_at', 'taked_at', 'cs_username', 'tecnician_username', 'total_cost', 'warranty'
    ];
    public $timestamps = false;

    private static array $searchAttributs = [];

    public function client()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function broken()
    {
        return $this->hasMany(Broken::class, 'service_id');
    }

    public function history()
    {
        return $this->hasMany(History::class, 'service_id');
    }

    public function setToSearchableArray(array $attributs)
    {
        self::$searchAttributs = $attributs;
    }

    #[SearchUsingPrefix(['code'])]
    #[SearchUsingFullText(['complaint', 'customers.name', 'products.name'])]
    public function toSearchableArray()
    {
        return self::$searchAttributs;
        // return [
        //     'code' => $this->code,
        //     'complaint' => $this->complaint,
        //     'customers.name' => '',
        //     'products.name' => '',
        // ];
    }
}
