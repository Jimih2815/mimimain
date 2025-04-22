<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model {
    protected $fillable = ['name','slug','description','base_price','img'];
  
    public function optionValues() {
      return $this->belongsToMany(OptionValue::class, 'product_option');
    }
  
    public function priceWithOptions(array $valueIds) {
      $extra = OptionValue::whereIn('id',$valueIds)->sum('extra_price');
      return $this->base_price + $extra;
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

  }
