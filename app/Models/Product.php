<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'description', 'price', 'category_id','stock','range_id','is_on_promotion','is_popular'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function range()
    {
        return $this->belongsTo(Range::class);
    }
}
