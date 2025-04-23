<?php
// app/helpers.php

use App\Models\WidgetPlacement;

if (! function_exists('widget_for')) {
    /**
     * Trả về Widget (có collection) đã gán cho region.
     */
    function widget_for(string $region)
    {
        $pl = WidgetPlacement::with('widget.collection')->find($region);
        return $pl ? $pl->widget : null;
    }
}
