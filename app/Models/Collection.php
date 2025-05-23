<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',        // nếu bạn có cột này
        'sort_order',   // nếu bạn có cột này
    ];

    /**
     * Bộ sưu tập này chứa nhiều products
     */
    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'collection_product'
        );
    }

    /**
     * Bộ sưu tập này thuộc về nhiều sector
     */
    public function sectors()
    {
        return $this->belongsToMany(
            Sector::class,
            'sector_collection',      // tên bảng pivot
            'collection_id',          // khóa ngoại trên pivot trỏ về collection
            'sector_id'               // khóa ngoại trên pivot trỏ về sector
        )
        ->withPivot(['custom_name','custom_image','sort_order'])
        ->withTimestamps();
    }
}
