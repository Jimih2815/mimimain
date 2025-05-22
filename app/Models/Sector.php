<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    protected $fillable = ['name', 'image', 'collection_id', 'sort_order'];

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }
}
