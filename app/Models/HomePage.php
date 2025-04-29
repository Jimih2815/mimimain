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
        'pre_banner_title',                  
        'pre_banner_button_text',            
        'pre_banner_button_collection_id',
        'collection_slider_title',
        'product_slider_title',   
        'collection_section_title',
        'collection_section_button_text',
        'collection_section_button_collection_id',
        'intro_text',
        'intro_button_text',
        'intro_button_collection_id',
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
    // Quan hệ cho nút bộ sưu tập
    public function collectionSectionCollection()
    {
        return $this->belongsTo(Collection::class, 'collection_section_button_collection_id');
    }
    // Quan hệ cho nút Khởi Đầu
    public function introButtonCollection()
    {
        return $this->belongsTo(Collection::class, 'intro_button_collection_id');
    }
}
