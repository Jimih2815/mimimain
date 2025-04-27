<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SidebarItem extends Model
{
    protected $fillable = ['name','parent_id','collection_id','sort_order'];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }
}
