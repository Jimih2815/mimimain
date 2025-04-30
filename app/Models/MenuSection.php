<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\MenuGroup;
use App\Models\Collection;

class MenuSection extends Model
{
    use HasFactory;

    /**
     * Các trường cho phép gán hàng loạt
     */
    protected $fillable = [
        'name',
        'slug',
        'sort_order',
        'collection_id',
    ];

    /**
     * Quan hệ 1-n với MenuGroup
     */
    public function groups()
    {
        return $this->hasMany(MenuGroup::class, 'menu_section_id')
                    ->orderBy('sort_order');
    }

    /**
     * Quan hệ ngược tới Collection (có thể null)
     */
    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }
}
