<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Collection;

class HomePage extends Model
{
    // Cho phép cập nhật hàng loạt
    protected $fillable = [
        'banner_image',
        'about_title',
        'about_text',
        'show_button',           // bật/tắt button
        'button_collection_id',  // gán Collection cho button
    ];

    /**
     * Quan hệ tới Collection mà button sẽ điều hướng
     */
    public function buttonCollection()
    {
        return $this->belongsTo(Collection::class, 'button_collection_id');
    }
}
