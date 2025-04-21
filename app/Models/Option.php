<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Option thuộc về 1 classification
    public function classification()
    {
        return $this->belongsTo(Classification::class);
    }
}
