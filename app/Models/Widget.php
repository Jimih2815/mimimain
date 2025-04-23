<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Widget extends Model
{
    protected $fillable = [
      'name','slug','type','collection_id',
      'image','button_text','html'
    ];

    public function collection()
    {
        return $this->belongsTo(\App\Models\Collection::class);
    }
}
