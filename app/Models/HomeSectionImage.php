<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class HomeSectionImage extends Model
{
    protected $fillable = ['position','image','collection_id'];

    public function collection()
    {
        return $this->belongsTo(\App\Models\Collection::class);
    }
}
