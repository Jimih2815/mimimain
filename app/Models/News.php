<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class News extends Model
{
    protected $fillable = ['title','slug','thumbnail','content'];

    // Tự động sinh slug từ title nếu chưa có
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
}
