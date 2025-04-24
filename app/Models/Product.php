<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OptionValue;
use App\Models\Category;
use App\Models\CategoryHeader;
use App\Models\Collection;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'base_price',
        'img',
        'sub_img',
    ];

    /** 
     * Cast sub_img thành array để Laravel tự encode/decode JSON
     */
    protected $casts = [
        'sub_img' => 'array',
    ];

    /**
     * Quan hệ many-to-many tới OptionValue qua pivot product_option
     */
    public function optionValues()
    {
        return $this->belongsToMany(OptionValue::class, 'product_option');
    }

    /**
     * Quan hệ many-to-many tới Category
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    /**
     * Quan hệ many-to-many tới CategoryHeader (mega-menu)
     */
    public function headerBoxes()
    {
        return $this->belongsToMany(CategoryHeader::class, 'category_header_product');
    }

    /**
     * Quan hệ many-to-many tới Collection
     */
    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'collection_product');
    }
}
