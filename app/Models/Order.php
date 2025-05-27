<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderNote;  // import class OrderNote

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'fullname',
        'phone',
        'address',
        // bỏ luôn cột `note` vì đã migrate sang order_notes
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
            if (! $order->order_code) {
                do {
                    $code = str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
                } while (self::where('order_code', $code)->exists());

                $order->order_code = $code;
            }
        });
    }

    /** Quan hệ về User */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Quan hệ nhiều OrderItem */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Quan hệ nhiều OrderNote để hiển thị chat hai chiều
     */
    public function notes()
    {
        return $this
            ->hasMany(OrderNote::class)
            ->orderBy('created_at', 'asc');
    }
}
