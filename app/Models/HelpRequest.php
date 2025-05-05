<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpRequest extends Model
{
    use HasFactory;

    public const STATUSES = ['Đã tiếp nhận','Đang xử lý','Hoàn thành'];

    protected $fillable = [
        'user_id','name','phone','message','response','status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
