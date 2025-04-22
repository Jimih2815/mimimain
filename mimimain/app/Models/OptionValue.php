<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OptionValue extends Model
{
    protected $fillable = ['option_type_id', 'value', 'extra_price'];

    public function type(): BelongsTo
    {
        return $this->belongsTo(OptionType::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_option');
    }
}
