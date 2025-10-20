<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = ['user_id', 'category_id', 'title', 'description', 'qty', 'price', 'img'];



    public function Category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
