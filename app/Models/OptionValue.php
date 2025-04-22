<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OptionValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'option_type_id',
        'value',
        'extra_price',
    ];

    public function type()
    {
        return $this->belongsTo(OptionType::class, 'option_type_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_option');
    }
}
