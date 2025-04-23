<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    protected $fillable = ['name','slug','description'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'collection_product');
    }
}
