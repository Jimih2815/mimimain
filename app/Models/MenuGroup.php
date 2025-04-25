<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// Import model liên quan
use App\Models\MenuSection;
use App\Models\Product;

class MenuGroup extends Model
{
    use HasFactory;

    /**
     * Những cột được mass-assign
     */
    protected $fillable = [
        'menu_section_id',
        'title',
        'sort_order',
    ];

    /**
     * Mỗi nhóm này thuộc về 1 section
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(MenuSection::class);
    }

    /**
     * Quan hệ nhiều-nhiều với Product qua pivot menu_group_product
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'menu_group_product')
                    ->withPivot('sort_order')
                    ->orderBy('menu_group_product.sort_order');
    }
}
