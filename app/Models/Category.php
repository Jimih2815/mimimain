<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Nếu cần: quan hệ đến products (nếu product gắn thẳng category)
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Quan hệ self‑reference, con của category
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Quan hệ self‑reference, parent
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
}
