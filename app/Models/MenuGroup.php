<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MenuGroup extends Model
{
    protected $fillable = ['menu_section_id','title','sort_order'];

    public function section(): BelongsTo
    {
        return $this->belongsTo(MenuSection::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'menu_group_product')
                ->withPivot('sort_order')
                ->orderBy('menu_group_product.sort_order');   // ✅ dùng tên cột thật
    }
}
