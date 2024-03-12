<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class range extends Model
{
    use HasFactory;
    protected $fillable=["places", 
       "category_id",
       "location"

    ];
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
