<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderNote extends Model
{
    /**
     * Các trường được phép gán (mass assignment)
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',    // ID của đơn hàng
        'user_id',     // ID của người tạo note (user hoặc admin)
        'is_admin',    // đánh dấu note do admin tạo
        'message',     // nội dung note
    ];

    /**
     * Quan hệ về Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Quan hệ về User (có thể null nếu là admin system)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}