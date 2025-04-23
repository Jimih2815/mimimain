<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WidgetPlacement extends Model
{
    public $incrementing = false;        // vì PK là string
    protected $primaryKey = 'region';
    protected $keyType    = 'string';

    protected $fillable = ['region','widget_id'];

    public function widget()
    {
        return $this->belongsTo(Widget::class);
    }
}
