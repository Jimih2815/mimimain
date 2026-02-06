<?php

namespace App\Models\ChamCong;

use Illuminate\Database\Eloquent\Model;

class ChamCongUser extends Model
{
    protected $connection = 'chamcong';
    protected $table = 'users';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'password',
        'employee_type',
        'base_salary',
        'required_hours',
        'hourly_rate',
        'ignore_location',
        'remember_token',
    ];
}
