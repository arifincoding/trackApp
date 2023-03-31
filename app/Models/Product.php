<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Laravel\Scout\Attributes\SearchUsingFullText;

class Product extends Model
{

    use HasFactory, Searchable;

    protected $fillable = ['name', 'category_id', 'product_defects', 'completeness', 'note'];
    public $timestamps = false;

    #[SearchUsingFullText(['name'])]
    public function toSearchableArray()
    {
        return [
            'name' => $this->name
        ];
    }
}
