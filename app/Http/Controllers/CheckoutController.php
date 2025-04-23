<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Order;
use App\Models\OptionValue;

class CheckoutController extends Controller
{
    /** 
     * GET  /checkout 
     */
    public function show(Request $request)
    {
        $selected = $request->input('selected_ids', []);
        $cart     = session('cart', []);

        $items = [];
        $grand = 0;
        foreach ($selected as $key) {
            if (isset($cart[$key])) {
                $items[$key] = $cart[$key];
                $grand += $cart[$key]['price'] * $cart[$key]['quantity'];
            }
        }
        if (empty($items)) {
            return redirect()->route('cart.index')
                             ->with('error','Bạn chưa chọn sản phẩm nào để thanh toán.');
        }

        $bankRef = $this->uniqueBankRef();
        session(['pending_bank_ref' => $bankRef]);

        $bankCode    = 'TCB';
        $accountNo   = '19032724004016';
        $accountName = 'PHAN THAO NGUYEN';
        $qrUrl = "https://img.vietqr.io/image/{$bankCode}-{$accountNo}-compact.png"
               . "?amount={$grand}"
               . "&addInfo=" . urlencode($bankRef)
               . "&accountName=" . urlencode($accountName);

        return view('checkout.show', compact('items','grand','qrUrl','bankRef'));
    }

    /**
     * POST /checkout/confirm
     */
    public function confirm(Request $r)
    {
        $r->validate([
            'fullname'       => 'required|string',
            'phone'          => 'required|string',
            'address'        => 'required|string',
            'note'           => 'nullable|string',
            'payment'        => 'required|in:cod,bank',
            'bank_ref'       => 'nullable|string',
        ]);

        $cart = session('cart', []);
        if (empty($cart)) {
            return back()->withErrors('Giỏ hàng trống!');
        }

        $total = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cart));
        $bankRef = $r->input('bank_ref') ?: session('pending_bank_ref');

        $order = Order::create([
            'fullname'       => $r->fullname,
            'phone'          => $r->phone,
            'address'        => $r->address,
            'note'           => $r->note,
            'payment_method' => $r->payment,                  
            'bank_ref'       => $r->payment==='bank' ? $bankRef : null,
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

        session()->forget(['cart','pending_bank_ref']);

        return view('checkout.thankyou', compact('order'));
    }

    /**
     * POST /checkout/bank-ref
     */
    public function ajaxBankRef(Request $r)
    {
        $r->validate(['amount'=>'required|integer']);
        $amount = $r->amount;
        $ref    = $this->uniqueBankRef();

        $payload = $this->vietQRPayloadRaw($amount, $ref);
        $svg     = QrCode::size(260)->generate($payload);

        session(['pending_bank_ref'=>$ref]);

        return response()->json(['ref'=>$ref,'qr'=>$svg]);
    }

    private function uniqueBankRef(): string
    {
        do {
            $ref = Str::upper(Str::random(10));
        } while (Order::where('bank_ref',$ref)->exists());
        return $ref;
    }

    private function vietQRPayloadRaw(int $amount, string $ref): string
    {
        $acc  = '19032724004016';
        $name = 'PHAN THAO NGUYEN';
        return "{$acc}|{$name}|{$amount}|{$ref}";
    }
}
