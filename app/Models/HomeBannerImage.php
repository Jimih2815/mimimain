<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeBannerImage extends Model
{
    protected $fillable = [
        'home_page_id',
        'device',
        'image',
        'collection_id',
        'sort_order',
    ];

    public function homePage()
    {
        return $this->belongsTo(HomePage::class);
    }

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }
}
