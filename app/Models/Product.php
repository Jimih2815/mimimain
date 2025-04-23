<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Imports cho các quan hệ
use App\Models\OptionValue;
use App\Models\Category;
use App\Models\CategoryHeader;

class Product extends Model
{
    use HasFactory;

    /**
     * Các trường được phép mass-assign
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'base_price',
        'img',
    ];

    /**
     * Quan hệ many-to-many tới OptionValue qua bảng pivot product_option
     */
    public function optionValues()
    {
        return $this->belongsToMany(
            OptionValue::class,
            'product_option'
        ); 
    }

    /**
     * Tính giá cuối = base_price + tổng extra_price của các OptionValue
     *
     * @param  int[]  $valueIds
     * @return float
     */
    public function priceWithOptions(array $valueIds): float
    {
        $extra = OptionValue::whereIn('id', $valueIds)
                            ->sum('extra_price');

        return $this->base_price + $extra;
    }

    /**
     * Quan hệ many-to-many tới Category (category_product)
     */
    public function categories()
    {
        return $this->belongsToMany(
            Category::class,
            'category_product'
        );
    }

    /**
     * Quan hệ many-to-many tới CategoryHeader (mega-menu)
     */
    public function headerBoxes()
    {
        return $this->belongsToMany(
            CategoryHeader::class,
            'category_header_product'
        );
    }
    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'collection_product');
    }

}
