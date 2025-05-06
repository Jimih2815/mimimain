<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    ];

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
