<?php

namespace App\Http\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

// dùng facade tĩnh của v2.x
use Intervention\Image\ImageManagerStatic as Image;

trait HandlesWebpUpload
{
    /**
     * Upload & convert UploadedFile sang WebP
     */
    protected function uploadAsWebp(UploadedFile $file, string $folder, int $quality = 80): string
    {
        // 1) tạo tên file slug-timestamp.webp
        $name     = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $slug     = Str::slug($name);
        $fileName = "{$slug}-".time().".webp";

        // 2) đọc ảnh gốc và encode sang WebP
        $image = Image::make($file->getRealPath())
                      ->encode('webp', $quality);

        // 3) xác định path lưu
        $path = trim($folder, '/')."/{$fileName}";

        // 4) ghi vào storage/app/public/...
        Storage::disk('public')->put($path, (string) $image);

        return $path;
    }
}
