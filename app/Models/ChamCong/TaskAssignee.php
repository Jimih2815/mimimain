<?php

namespace App\Models\ChamCong;

use Illuminate\Database\Eloquent\Model;

class TaskAssignee extends Model
{
    protected $connection = 'chamcong';
    protected $table = 'task_assignees';
    public $timestamps = false;

    protected $fillable = [
        'task_id',
        'user_id',
        'assigned_at',
        'seen',
    ];
}
