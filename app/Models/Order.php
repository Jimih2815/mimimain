<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'fullname',
        'phone',
        'address',
        'note',
        'payment_method',  // ← trước đây là 'payment'
        'bank_ref',
        'total',
    ];
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
