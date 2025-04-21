<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classification extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Mỗi variant thuộc 1 product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Mỗi variant có nhiều option (size, màu, ...)
    public function options()
    {
        return $this->hasMany(Option::class);
    }
}
