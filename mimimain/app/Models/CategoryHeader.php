<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoryHeader extends Model {
    use HasFactory;
    protected $fillable = ['category_id','title','sort_order'];

    public function category() {
        return $this->belongsTo(Category::class);
    }
    public function products() {
        return $this->belongsToMany(Product::class, 'category_header_product');
    }
}
