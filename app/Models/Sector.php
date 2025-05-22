<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'image',
        'sort_order',
    ];

    /**
     * Các collection được gắn vào sector này
     */
    public function collections()
    {
        return $this->belongsToMany(\App\Models\Collection::class, 'sector_collection')
                    ->withPivot(['custom_name','custom_image','sort_order'])
                    ->withTimestamps();
    }
}
