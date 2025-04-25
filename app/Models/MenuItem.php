<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = ['menu_section_id','label','url','sort_order'];
    public $timestamps = false;

    public function products()
    {
        return $this->belongsToMany(Product::class, 'menu_group_product')
                    ->withPivot('sort_order')
                    ->orderBy('menu_group_product.sort_order');
    }
}
