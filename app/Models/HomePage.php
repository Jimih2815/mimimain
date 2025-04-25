<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Collection;

class HomePage extends Model
{
    // Copy 100% file này vào app/Models/HomePage.php
    protected $fillable = [
        'banner_image',
        'about_title',
        'about_text',
        'show_button',
        'button_collection_id',
        'button_text',           // <-- mới thêm
    ];

    /**
     * Quan hệ tới Collection được chọn cho nút trung tâm
     */
    public function buttonCollection()
    {
        return $this->belongsTo(Collection::class, 'button_collection_id');
    }
}
