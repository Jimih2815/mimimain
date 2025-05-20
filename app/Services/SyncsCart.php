<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\CartItem;
use App\Models\OptionValue;

trait SyncsCart
{
    /**
     * Merge toàn bộ CartItem trong DB của user vào session['cart']
     */
      protected function mergeDBCartIntoSession(): void
    {
        if (! Auth::check()) {
            return;
        }

        $newSession = [];   // reset hoàn toàn

        $dbItems = CartItem::with('product')
                   ->where('user_id', Auth::id())
                   ->get();

        foreach ($dbItems as $it) {
            $optIds = $it->options ?? [];

            // 1) tính price
            $extra = OptionValue::whereIn('id', $optIds)->sum('extra_price');
            $price = $it->product->base_price + $extra;

            // 2) sinh key duy nhất
            $key = md5($it->product_id . '|' . json_encode($optIds));

            // 3) chọn ảnh (option ưu tiên)
            $image = $it->product->img;
            if (! empty($optIds)) {
                $first = OptionValue::find(array_values($optIds)[0]);
                if ($first && $first->option_img) {
                    $image = $first->option_img;
                }
            }

            // 4) gán vào mảng mới
            $newSession[$key] = [
                'product_id' => $it->product_id,
                'quantity'   => $it->quantity,
                'price'      => $price,
                'options'    => $optIds,
                'name'       => $it->product->name,
                'image'      => $image,
            ];
        }

        // 5) ghi đè session cũ
        session(['cart' => $newSession]);
    }
    /**
     * Đồng bộ session['cart'] → bảng cart_items (DB):
     * - Xóa những mục DB không còn trong session
     * - Tạo / cập nhật những mục session mới
     */
    protected function syncCartToDB(array $sessionCart): void
    {
        if (! Auth::check()) {
            return;
        }

        $userId      = Auth::id();
        $dbItems     = CartItem::where('user_id', $userId)->get();
        $sessionKeys = array_keys($sessionCart);

        // Xóa DB items không còn trong session
        foreach ($dbItems as $db) {
            $optIds = $db->options ?? [];
            $key    = md5($db->product_id . '|' . json_encode($optIds));

            if (! in_array($key, $sessionKeys, true)) {
                $db->delete();
            }
        }

        // Cập nhật hoặc tạo mới
        foreach ($sessionCart as $key => $row) {
            CartItem::updateOrCreate(
                [
                    'user_id'    => $userId,
                    'product_id' => $row['product_id'],
                    'options'    => $row['options'] ?? [],
                ],
                [
                    'quantity'   => $row['quantity'],
                ]
            );
        }
    }
}
