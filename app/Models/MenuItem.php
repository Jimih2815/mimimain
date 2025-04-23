<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItem extends Model
{
    protected $fillable = ['menu_section_id','label','url','sort_order'];

    public function section(): BelongsTo
    {
        return $this->belongsTo(MenuSection::class,'menu_section_id');
    }
}
