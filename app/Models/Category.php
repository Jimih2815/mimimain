<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CategoryHeader;  // import relation

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name','slug'];

    /**
     * Quan hệ đến bảng category_headers (Mega-menu headers).
     */
    public function headers()
    {
        return $this->hasMany(CategoryHeader::class, 'category_id')
                    ->orderBy('sort_order');
    }

    /**
     * Quan hệ many-to-many đến products (pivot category_product).
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_product');
    }
}
