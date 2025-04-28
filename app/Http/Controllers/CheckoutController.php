<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Order;
use App\Models\Product;
use App\Models\OptionValue;

class CheckoutController extends Controller
{
    /**
     * GET /checkout
     */
    public function show(Request $request)
    {
        $selected = $request->input('selected_ids', []);
        $cart     = session('cart', []);

        // Nếu Buy Now thì session('cart') chỉ chứa 1 item, ngược lại dùng selected_ids
        if (empty($selected) && ! empty($cart)) {
            $items = $cart;
        } else {
            $items = [];
            foreach ($selected as $key) {
                if (isset($cart[$key])) {
                    $items[$key] = $cart[$key];
                }
            }
        }

        if (empty($items)) {
            return redirect()->route('cart.index')
                             ->with('error', 'Bạn chưa chọn sản phẩm nào để thanh toán.');
        }

        // Tính GRAND = (base_price + extra_price) * quantity
        $grand = 0;
        foreach ($items as $item) {
            $unitPrice = $item['price'] + ($item['extra_price'] ?? 0);
            $grand    += $unitPrice * $item['quantity'];
        }

        // Tính phí ship: miễn phí nếu >=199k hoặc giỏ trống
        $shipping = ($grand === 0 || $grand > 199000) ? 0 : 20000;

        // Tổng thực tế để tạo QR
        $amountForQr = $grand + $shipping;

        // Tạo mã tham chiếu ngân hàng & QR URL
        $bankRef = $this->uniqueBankRef();
        session(['pending_bank_ref' => $bankRef]);

        $bankCode    = 'TCB';
        $accountNo   = '19032724004016';
        $accountName = 'PHAN THAO NGUYEN';
        $qrUrl = "https://img.vietqr.io/image/{$bankCode}-{$accountNo}-compact.png"
               . "?amount={$amountForQr}"
               . "&addInfo=" . urlencode($bankRef)
               . "&accountName=" . urlencode($accountName);

        return view('checkout.show', [
            'items'       => $items,
            'grand'       => $grand,
            'shipping'    => $shipping,
            'qrUrl'       => $qrUrl,
            'bankRef'     => $bankRef,
        ]);
    }

    /**
     * POST /checkout/buy-now/{product}
     */
    public function buyNow(Request $request, Product $product)
    {
        $options = $request->input('options', []);
        session()->forget('cart');

        $sumExtra = 0;
        foreach ($options as $typeId => $valId) {
            if ($opt = OptionValue::find($valId)) {
                $sumExtra += $opt->extra_price;
            }
        }

        $imgPath = $product->img;
        if ($first = reset($options)) {
            if ($opt = OptionValue::find($first) and $opt->option_img) {
                $imgPath = $opt->option_img;
            }
        }

        session()->push('cart', [
            'product_id'  => $product->id,
            'name'        => $product->name,
            'quantity'    => 1,
            'price'       => $product->base_price,
            'extra_price' => $sumExtra,
            'total_price' => $product->base_price + $sumExtra,
            'image'       => $imgPath,
            'options'     => $options,
        ]);

        return redirect()->route('checkout.show');
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

        $cart = session('cart', []);
        if (empty($cart)) {
            return back()->withErrors('Giỏ hàng trống!');
        }

        $total = array_sum(array_map(function($i) {
            return ($i['price'] + ($i['extra_price'] ?? 0)) * $i['quantity'];
        }, $cart));

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

        foreach ($cart as $key => $item) {
            $order->items()->create([
                'product_id' => $item['product_id'] ?? $key,
                'quantity'   => $item['quantity'],
                'price'      => $item['price'],
                'options'    => $item['options'] ?? [],
            ]);
        }

        session()->forget(['cart', 'pending_bank_ref']);

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
     * POST /checkout/bank-ref
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
