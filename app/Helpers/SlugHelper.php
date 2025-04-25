<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class SlugHelper
{
    public static function unique(Model $model, string $name, string $field='slug'): string
    {
        $base = Str::slug($name) ?: Str::random(6);
        $slug = $base;  $i = 1;

        while ($model->where($field,$slug)->exists()) {
            $slug = $base.'-'.$i++;
        }
        return $slug;
    }
}
