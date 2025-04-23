<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Product;
use App\Models\Order;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * Nếu bạn không muốn lưu created_at/updated_at
     */
    public $timestamps = false;

    /**
     * Các trường cho phép mass-assignment
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'options',   // ← thêm vào đây
    ];

    /**
     * Cast cột options từ JSON sang array PHP
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array',
    ];

    /**
     * Quan hệ ngược về Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Quan hệ ngược về Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
