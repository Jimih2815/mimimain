<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name','slug'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_product');
    }
    public function headers()
    {
        return $this->hasMany(CategoryHeader::class)->orderBy('sort_order');
    }
}
