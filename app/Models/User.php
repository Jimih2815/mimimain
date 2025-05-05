<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Order;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        // ...
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Các đơn hàng của user
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function helpRequests()
    {
        return $this->hasMany(HelpRequest::class);
    }
    public function favorites()
{
    return $this->belongsToMany(Product::class, 'favorite_product')
                ->withTimestamps();
}

}
