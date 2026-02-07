<?php

namespace App\Models\ChamCong;

use Illuminate\Database\Eloquent\Model;

class AttendanceRequest extends Model
{
    protected $connection = 'chamcong';
    protected $table = 'attendance_requests';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'work_date',
        'check_in',
        'check_out',
        'total_minutes',
        'status',
        'created_at',
    ];
}
