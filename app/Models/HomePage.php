<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Collection;

class HomePage extends Model
{
    protected $fillable = [
        'banner_image',
        'about_title',
        'about_text',
        'show_button',
        'button_collection_id',
        'button_text',
        'pre_banner_title',                  // ← mới
        'pre_banner_button_text',            // ← mới
        'pre_banner_button_collection_id',   // ← mới
    ];

    // Quan hệ nút chính giữa banner
    public function buttonCollection()
    {
        return $this->belongsTo(Collection::class, 'button_collection_id');
    }

    // Quan hệ nút ở phần trước banner
    public function preBannerCollection()
    {
        return $this->belongsTo(Collection::class, 'pre_banner_button_collection_id');
    }
}
