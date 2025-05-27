<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'fullname',
        'phone',
        'address',
        'note',
        'payment_method',
        'tracking_number',
        'bank_ref',
        'total',
        'order_code', 
        'status', 
    ];
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($order) {
            // Nếu chưa có code thì sinh
            if (! $order->order_code) {
                do {
                    // Sinh ngẫu nhiên 8 chữ số, có thể có số 0 ở đầu
                    $code = str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
                } while (self::where('order_code', $code)->exists());

                $order->order_code = $code;
            }
        });
    }
    // Quan hệ ngược về User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Order có nhiều items
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
