<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class News extends Model
{
    // Thêm collection_id vào đây
    protected $fillable = [
        'title',
        'slug',
        'thumbnail',
        'content',
        'collection_id',   // ← mới thêm
    ];

    public static function booted()
    {
        static::creating(function($n){
            $n->slug = Str::slug($n->title).'-'.time();
        });
        static::updating(function($n){
            if ($n->isDirty('title')) {
                $n->slug = Str::slug($n->title).'-'.time();
            }
        });
    }

    public function collection()
    {
        return $this->belongsTo(\App\Models\Collection::class);
    }
}
