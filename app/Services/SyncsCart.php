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

        // Cập nhật hoặc tạo mới (chưa lưu price ở đây là ok, chúng ta recalc khi merge)
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
            // tính extra_price từ OptionValue
            $extra = OptionValue::whereIn('id', $optIds)->sum('extra_price');

            $price = $it->product->base_price + $extra;
            $key   = md5($it->product_id . '|' . json_encode($optIds));

            $session[$key] = [
                'product_id' => $it->product_id,
                'quantity'   => $it->quantity,
                'price'      => $price,
                'options'    => $optIds,
                'name'       => $it->product->name,
                'image'      => $it->product->img,
            ];
        }

        session(['cart' => $session]);
    }
}
