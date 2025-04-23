<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\OptionType;
use App\Models\Product;

class OptionValue extends Model
{
    use HasFactory;

    /**
     * Các trường được phép mass-assign
     *
     * @var array
     */
    protected $fillable = [
        'option_type_id',
        'value',
        'extra_price',
    ];

    /**
     * Quan hệ ngược về OptionType
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(OptionType::class, 'option_type_id');
    }

    /**
     * Quan hệ many-to-many tới Product qua pivot product_option
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'product_option',
            'option_value_id',
            'product_id'
        );
    }
}
