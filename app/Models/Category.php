<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Laravel\Scout\Attributes\SearchUsingFullText;
use Laravel\Scout\Attributes\SearchUsingPrefix;

class Category extends Model
{
    use HasFactory, Searchable;

    protected $fillable = ['name'];
    public $timestamps = false;

    #[SearchUsingPrefix(['id'])]
    #[SearchUsingFullText(['name'])]
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }
}
