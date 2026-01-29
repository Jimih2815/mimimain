Mimi Mobile Home v2 — CHỈ FILE ĐÃ SỬA

Cách dùng:
1) Giải nén zip này vào thư mục dự án Laravel của bạn (đè đúng đường dẫn).
2) Chạy migration:
   php artisan migrate

File có trong gói:
- database/migrations/2026_01_29_150000_add_banner_image_mobile_to_home_pages_table.php
php artisan migrate --path=database/migrations/2026_01_29_150000_add_banner_image_mobile_to_home_pages_table.php

- app/Models/HomePage.php
- app/Http/Controllers/Admin/HomePageController.php
- resources/views/admin/home/edit.blade.php
- resources/views/home-mobile.blade.php
- resources/js/app-mobile.js

Ghi chú:
- Đã thêm field banner_image_mobile cho banner dọc mobile.
- Trang home mobile: banner full dọc, section slider-full-width đổi thành list xếp dọc (không slider).
