<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;        // import thêm
use App\Models\Order;          // nếu bạn cần quan hệ ngược

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
    ];

    // ========== Quan hệ với đơn hàng ==========
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // ====== Quan hệ với sản phẩm (thêm cái này) ======
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
