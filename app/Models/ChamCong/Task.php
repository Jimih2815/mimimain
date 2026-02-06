<?php

namespace App\Models\ChamCong;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $connection = 'chamcong';
    protected $table = 'tasks';
    public $timestamps = false;

    protected $fillable = [
        'task_name',
        'task_content',
        'due_date',
        'general_note',
        'created_by',
        'created_at',
        'updated_at',
        'completed_at',
        'completion_log',
        'admin_popup_shown',
    ];
}
