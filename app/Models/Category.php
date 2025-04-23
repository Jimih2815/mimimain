<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name','slug'];

    /* === XOÁ 2 DÒNG DƯỚI === */
    // use App\Models\CategoryHeader;
    // public function headers() { /* … */ }

    /* giữ lại quan hệ products nếu bạn vẫn cần */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_product');
    }
}
