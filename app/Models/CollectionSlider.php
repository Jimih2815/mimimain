<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionSlider extends Model
{
    protected $fillable = [
      'collection_id', 'image', 'text', 'sort_order'
    ];

    public function collection()
    {
        return $this->belongsTo(\App\Models\Collection::class);
    }
}
