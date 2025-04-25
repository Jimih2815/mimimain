<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// ❤️ Import model đúng
use App\Models\MenuGroup;

class MenuSection extends Model
{
    use HasFactory;

    protected $fillable = ['name','slug','sort_order'];

    /**
     * Mỗi section sẽ có nhiều menu-groups (bảng menu_groups), không phải MenuItem
     */
    public function groups()
    {
        return $this->hasMany(MenuGroup::class, 'menu_section_id')
                    ->orderBy('sort_order');
    }
}
