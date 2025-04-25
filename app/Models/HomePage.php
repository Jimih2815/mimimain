<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomePage extends Model
{
    // cho phép cập nhật hàng loạt các cột này
    protected $fillable = [
        'banner_image',
        'about_title',
        'about_text',
    ];
}
