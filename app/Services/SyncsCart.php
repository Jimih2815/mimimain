<?php
// app/Services/SyncsCart.php
namespace App\Services;

use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

trait SyncsCart
{
    /** Lưu session cart xuống DB */
    protected function syncCartToDB(array $cart): void
    {
        if (!Auth::check()) return;

        $userId    = Auth::id();
        $productId = array_keys($cart);

        // xoá sản phẩm không còn trong session
        CartItem::where('user_id', $userId)
                ->whereNotIn('product_id', $productId)
                ->delete();

        foreach ($cart as $pid => $row) {
            CartItem::updateOrCreate(
                ['user_id' => $userId, 'product_id' => $pid],
                ['quantity' => $row['quantity'], 'options' => $row['options'] ?? null]
            );
        }
    }

    /** Lấy DB cart + gộp với session (+) */
    protected function mergeDBCartIntoSession(): void
    {
        if (!Auth::check()) return;

        $session = session('cart', []);
        $dbItems = CartItem::with('product')
                    ->where('user_id', Auth::id())->get();

        foreach ($dbItems as $item) {
            $pid = $item->product_id;

            // ----- dữ liệu bảo đảm đồng nhất -----
            $row = [
                'name'     => $item->product->name,
                'price'    => $item->product->base_price,
                'quantity' => $item->quantity,
                'image'    => $item->product->img,
            ];

            /* Nếu bạn muốn "lấy lớn nhất" thì:
            $row['quantity'] = max($item->quantity, $session[$pid]['quantity'] ?? 0);
            */

            $session[$pid] = $row;      // ✨ GHI ĐÈ, không còn cộng dồn
        }

        session(['cart' => $session]);
    }
}
