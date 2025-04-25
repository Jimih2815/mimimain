<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

// Models liên quan
use App\Models\Category;
use App\Models\Product;
use App\Models\MenuSection;
use App\Models\MenuGroup;

class CategoryGroup extends Model
{
    use HasFactory;

    /**
     * Những cột cho phép mass-assignment
     */
    protected $fillable = [
        'category_id',
        'menu_group_id',
        'title',
        'sort_order',
    ];

    /**
     * Một Group thuộc về một Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Quan hệ many-to-many với Product qua pivot category_group_product
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_group_product')
                    ->withPivot('sort_order')
                    ->orderBy('category_group_product.sort_order');
    }

    /**
     * Tự động đồng bộ lên bảng mega-menu (menu_groups)
     */
    protected static function booted(): void
    {
        // Khi tạo Group mới
        static::created(function (CategoryGroup $grp) {
            DB::transaction(function () use ($grp) {
                // Tạo hoặc lấy MenuSection tương ứng
                $sec = MenuSection::firstOrCreate(
                    ['slug'       => $grp->category->slug],
                    ['name'       => $grp->category->name,
                     'sort_order' => $grp->category->sort_order ?? 0]
                );

                // Tạo MenuGroup (bảng menu_groups)
                $menuGroup = MenuGroup::create([
                    'menu_section_id' => $sec->id,
                    'title'           => $grp->title,
                    'sort_order'      => $grp->sort_order,
                ]);

                // Ghi lại khóa lên CategoryGroup
                $grp->updateQuietly(['menu_group_id' => $menuGroup->id]);
            });
        });

        // Khi update Group
        static::updated(function (CategoryGroup $grp) {
            DB::transaction(function () use ($grp) {
                MenuGroup::where('id', $grp->menu_group_id)
                         ->update([
                             'title'      => $grp->title,
                             'sort_order' => $grp->sort_order,
                         ]);
            });
        });

        // Khi xóa Group
        static::deleted(function (CategoryGroup $grp) {
            MenuGroup::where('id', $grp->menu_group_id)->delete();
        });
    }
}
