<?php

namespace App\Models\ChamCong;

use Illuminate\Database\Eloquent\Model;

class TaskProgress extends Model
{
    protected $connection = 'chamcong';
    protected $table = 'task_progress';
    public $timestamps = false;

    protected $fillable = [
        'task_id',
        'progress_content',
        'progress_note',
        'due_date',
        'is_completed',
        'completed_at',
    ];
}
