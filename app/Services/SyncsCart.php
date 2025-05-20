<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\CartItem;
use App\Models\OptionValue;

trait SyncsCart
{
    /**
     * Lưu session cart xuống DB (kèm options)
     */
    protected function syncCartToDB(array $cart): void
    {
        if (! Auth::check()) {
            return;
        }

        $userId     = Auth::id();
        $productIds = array_map(fn($row) => $row['product_id'], $cart);

        // Xóa những item đã bị remove khỏi session
        CartItem::where('user_id', $userId)
                ->whereNotIn('product_id', $productIds)
                ->delete();

        // Cập nhật hoặc tạo mới
        foreach ($cart as $row) {
            CartItem::updateOrCreate(
                [
                    'user_id'    => $userId,
                    'product_id' => $row['product_id'],
                ],
                [
                    'quantity' => $row['quantity'],
                    'options'  => $row['options'] ?? null,
                ]
            );
        }
    }

    /**
     * Merge DB cart vào session khi user đã login
     * — recalc price = base_price + extra_price(options)
     * — **LẤY ẢNH OPTION** thay vì ảnh gốc
     */
    protected function mergeDBCartIntoSession(): void
    {
        if (! Auth::check()) {
            return;
        }

        $session = session('cart', []);
        $dbItems = CartItem::with('product')
                    ->where('user_id', Auth::id())
                    ->get();

        foreach ($dbItems as $it) {
            $optIds = $it->options ?? [];

            // 1) Tính extra price
            $extra = OptionValue::whereIn('id', $optIds)->sum('extra_price');

            $price = $it->product->base_price + $extra;
            $key   = md5($it->product_id . '|' . json_encode($optIds));

            // 2) CHỌN ẢNH: ưu tiên ảnh của option đầu tiên
            $imagePath = $it->product->img;  // default
            if (! empty($optIds)) {
                // Lấy ID của option đầu tiên (bất kể key gì)
                $firstValId = array_values($optIds)[0];
                $firstVal   = OptionValue::find($firstValId);
                if ($firstVal && $firstVal->option_img) {
                    $imagePath = $firstVal->option_img;
                }
            }

            // 3) Gán vào session
            $session[$key] = [
                'product_id' => $it->product_id,
                'quantity'   => $it->quantity,
                'price'      => $price,
                'options'    => $optIds,
                'name'       => $it->product->name,
                'image'      => $imagePath,   // ← ảnh đã được chọn ở bước 2
            ];
        }

        session(['cart' => $session]);
    }
}
