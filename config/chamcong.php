<?php

return [
    'timezone' => env('CHAMCONG_TIMEZONE', 'Asia/Ho_Chi_Minh'),

    'office' => [
        'lat' => (float) env('CHAMCONG_OFFICE_LAT', 21.028320),
        'lng' => (float) env('CHAMCONG_OFFICE_LNG', 105.742490),
        'radius_km' => (float) env('CHAMCONG_OFFICE_RADIUS_KM', 1),
    ],
];
