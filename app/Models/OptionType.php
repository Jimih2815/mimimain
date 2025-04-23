<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\OptionValue;

class OptionType extends Model
{
    use HasFactory;

    /**
     * Các trường được phép mass-assign
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Quan hệ 1-n tới OptionValue
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function values()
    {
        return $this->hasMany(OptionValue::class, 'option_type_id');
    }
}
