<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Laravel\Scout\Attributes\SearchUsingFullText;

class Broken extends Model
{

    use HasFactory, Searchable;

    protected $fillable = ['title', 'is_approved', 'description', 'service_id', 'cost'];
    public $timestamps = false;

    #[SearchUsingFullText(['title', 'description'])]
    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'description' => $this->description
        ];
    }
}
