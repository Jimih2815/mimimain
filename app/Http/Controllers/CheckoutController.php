<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Order;
use App\Models\Product;
use App\Models\OptionValue;
use Jenssegers\Agent\Agent;

class CheckoutController extends Controller
{
    /**
     * GET /checkout
     */
    public function show(Request $request)
    {
        // MODE:
        // - cart     : thanh toán giỏ hàng như bình thường
        // - buy_now  : thanh toán 1 sản phẩm (mua ngay) nhưng KHÔNG đụng vào giỏ hàng
        $mode = $request->query('mode') === 'buy_now' ? 'buy_now' : 'cart';

        
        $selected = [];
if ($mode === 'buy_now') {
            $items = session('buy_now', []);

            if (empty($items)) {
                return redirect()
                    ->route('cart.index')
                    ->with('error', 'Sản phẩm mua ngay đã hết hiệu lực. Vui lòng bấm “Mua ngay” lại.');
            }
        } else {
            // nếu user vào checkout từ giỏ hàng, dọn sạch state mua ngay để khỏi bị dính
            $request->session()->forget('buy_now');

            // 1) Lấy danh sách sản phẩm được chọn (nếu có) từ giỏ
            $selected = $request->input('selected_ids', []);
            $cart     = session('cart', []);
            $items    = empty($selected)
                      ? $cart
                      : array_intersect_key($cart, array_flip($selected));

            if (empty($items)) {
                return redirect()
                    ->route('cart.index')
                    ->with('error', 'Bạn chưa chọn sản phẩm nào để thanh toán.');
            }
        }

        // 2. Tính tổng tiền sản phẩm
        $grand = 0;
        foreach ($items as $item) {
            $unitPrice = $item['price'] + ($item['extra_price'] ?? 0);
            $grand    += $unitPrice * $item['quantity'];
        }

        // 3. Tính phí ship: nếu >0 và <=199.000 thì +20k, ngược lại miễn phí
        $shipping    = ($grand > 0 && $grand <= 199_000) ? 20_000 : 0;
        $amountForQr = $grand + $shipping;

        // 4. Sinh mã tham chiếu ngân hàng và lưu tạm
        $bankRef = $this->uniqueBankRef();
        session(['pending_bank_ref' => $bankRef]);

        // 5. Tạo URL QR Code (VietQR)
        $bankCode    = 'TCB';
        $accountNo   = '19032724004016';
        $accountName = 'PHAN THAO NGUYEN';
        $qrUrl = "https://img.vietqr.io/image/{$bankCode}-{$accountNo}-compact.png"
               . "?amount={$amountForQr}"
               . "&addInfo="  . urlencode($bankRef)
               . "&accountName=" . urlencode($accountName);

        // 6. Chọn view desktop hoặc mobile
        $agent = new Agent();
        $view  = ($agent->isMobile() && ! $agent->isTablet())
               ? 'checkout.show-mobile'
               : 'checkout.show';

        return view($view, compact('items', 'grand', 'shipping', 'qrUrl', 'bankRef', 'mode', 'selected'));
    }

    /**
     * POST /checkout/buy-now/{product}
     * → Thanh toán ngay 1 sản phẩm nhưng KHÔNG xoá / đụng vào giỏ hàng
     */
    public function buyNow(Request $request, Product $product)
    {
        // Đánh dấu để middleware skip merge DB cart ở lần redirect kế tiếp (an toàn)
        $request->session()->put('skip_cart_sync', true);

        $options = $request->input('options', []);

        // Tính tổng extra_price
        $sumExtra = 0;
        foreach ($options as $typeId => $valId) {
            if ($opt = OptionValue::find($valId)) {
                $sumExtra += $opt->extra_price;
            }
        }

        // Chọn ảnh hiển thị
        $imgPath = $product->img;
        if ($first = reset($options)) {
            if ($opt = OptionValue::find($first) and $opt->option_img) {
                $imgPath = $opt->option_img;
            }
        }

        // Lưu riêng session buy_now (không đụng cart)
        $buyNowItem = [
            'product_id'  => $product->id,
            'name'        => $product->name,
            'quantity'    => 1,
            'price'       => $product->base_price,
            'extra_price' => $sumExtra,
            'total_price' => $product->base_price + $sumExtra,
            'image'       => $imgPath,
            'options'     => $options,
        ];

        session(['buy_now' => [$buyNowItem]]);

        // chuyển qua checkout ở chế độ buy_now
        return redirect()->route('checkout.show', ['mode' => 'buy_now']);
    }

    /**
     * POST /checkout/confirm
     */
    public function confirm(Request $r)
    {
        $r->validate([
            'fullname' => 'required|string',
            'phone'    => 'required|string',
            'address'  => 'required|string',
            'note'     => 'nullable|string',
            'payment'  => 'required|in:cod,bank',
            'bank_ref' => 'nullable|string',
        ]);

        $mode = $r->input('mode', 'cart') === 'buy_now' ? 'buy_now' : 'cart';

        $selected = $r->input('selected_ids', []);
        $cartAll  = session('cart', []);

        $items = $mode === 'buy_now'
            ? session('buy_now', [])
            : (empty($selected) ? $cartAll : array_intersect_key($cartAll, array_flip($selected)));

        if (empty($items)) {
            return back()->withErrors($mode === 'buy_now' ? 'Sản phẩm mua ngay trống!' : 'Giỏ hàng trống!');
        }

        $total = array_sum(array_map(function($i) {
            return ($i['price'] + ($i['extra_price'] ?? 0)) * $i['quantity'];
        }, $items));

        $bankRef = $r->input('bank_ref') ?: session('pending_bank_ref');

        $order = Order::create([
            'user_id'        => Auth::id(),
            'fullname'       => $r->fullname,
            'phone'          => $r->phone,
            'address'        => $r->address,
            'note'           => $r->note,
            'payment_method' => $r->payment,
            'bank_ref'       => $r->payment === 'bank' ? $bankRef : null,
            'total'          => $total,
        ]);

        foreach ($items as $key => $item) {
            $order->items()->create([
                'product_id' => $item['product_id'] ?? $key,
                'quantity'   => $item['quantity'],
                'price'      => $item['price'],
                'options'    => $item['options'] ?? [],
            ]);
        }

        // Chỉ xoá state tương ứng, không đụng giỏ hàng nếu là mua ngay
        if ($mode === 'buy_now') {
            session()->forget(['buy_now', 'pending_bank_ref']);
        } else {
            // Checkout từ giỏ hàng:
            // - Nếu có chọn sản phẩm: chỉ xoá đúng các sản phẩm đã thanh toán
            // - Nếu không truyền selected_ids: hiểu là thanh toán toàn bộ giỏ, xoá sạch
            if (!empty($selected)) {
                foreach ($selected as $k) {
                    unset($cartAll[$k]);
                }
                session(['cart' => $cartAll]);
                session()->forget(['pending_bank_ref']);
            } else {
                session()->forget(['cart', 'pending_bank_ref']);
            }
        }

        return redirect()->route('checkout.success', $order->id);
    }

    /**
     * GET /checkout/success/{order}
     */
    public function success(Order $order)
    {
        return view('checkout.thankyou', compact('order'));
    }

    /**
     * POST /checkout/bank-ref (AJAX)
     */
    public function ajaxBankRef(Request $r)
    {
        $r->validate(['amount' => 'required|integer']);
        $ref     = $this->uniqueBankRef();
        $payload = $this->vietQRPayloadRaw($r->amount, $ref);
        $svg     = QrCode::size(260)->generate($payload);

        session(['pending_bank_ref' => $ref]);
        return response()->json(['ref' => $ref, 'qr' => $svg]);
    }

    private function uniqueBankRef(): string
    {
        do {
            $ref = Str::upper(Str::random(10));
        } while (Order::where('bank_ref', $ref)->exists());

        return $ref;
    }

    private function vietQRPayloadRaw(int $amount, string $ref): string
    {
        $acc  = '19032724004016';
        $name = 'PHAN THAO NGUYEN';

        return "{$acc}|{$name}|{$amount}|{$ref}";
    }
}
