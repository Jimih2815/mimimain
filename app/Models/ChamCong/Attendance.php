<?php

namespace App\Models\ChamCong;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $connection = 'chamcong';
    protected $table = 'attendance';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'check_in',
        'ip_in',
        'check_out',
        'ip_out',
        'lat_in',
        'lng_in',
        'lat_out',
        'lng_out',
    ];
}
