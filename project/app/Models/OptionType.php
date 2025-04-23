<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OptionType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Các giá trị (value) thuộc type này
    public function values()
    {
        return $this->hasMany(OptionValue::class);
    }
}
