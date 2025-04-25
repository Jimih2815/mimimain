<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    /* -------------------------------------------------------------------
     |  Thuộc tính
     |--------------------------------------------------------------------*/
    protected $fillable = ['name', 'slug', 'sort_order'];

    /*  Model binding: /categories/{category:slug}  */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /* -------------------------------------------------------------------
     |  Quan hệ
     |--------------------------------------------------------------------*/
    /** Nhiều “mục dropdown” trong header */
    public function groups()
    {
        return $this->hasMany(CategoryGroup::class)->orderBy('sort_order');
    }

    /** Giữ lại nếu bạn vẫn cần Category ↔ Product trực tiếp */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_product');
    }

    /* -------------------------------------------------------------------
     |  Boot – auto slug + sync MenuSection
     |--------------------------------------------------------------------*/
    protected static function booted(): void
    {
        /* 1) Tự sinh slug nếu chưa có */
        static::creating(function ($cat) {
            $cat->slug = $cat->slug ?: Str::slug($cat->name);
        });

        static::saving(function ($cat) {
            if (!$cat->slug) {
                $cat->slug = Str::slug($cat->name);
            }
        });

        /* 2) Đồng bộ sang menu_sections để header dùng */
        static::saved(function ($cat) {
            \App\Models\MenuSection::updateOrCreate(
                ['slug' => $cat->slug],
                ['name' => $cat->name, 'sort_order' => $cat->sort_order ?? 0]
            );
        });

        /* 3) Xóa Category → xoá luôn Section tương ứng */
        static::deleted(function ($cat) {
            \App\Models\MenuSection::where('slug', $cat->slug)->delete();
        });
    }
}
