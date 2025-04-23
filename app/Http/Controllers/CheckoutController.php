<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Order;

class CheckoutController extends Controller
{
    /** 
     * GET  /checkout 
     * Hiển thị form + QR tĩnh VietQR.io (compact, với mã đã sinh sẵn).
     */
    public function show(Request $request)
    {
        $selected = $request->input('selected_ids', []);
        $cart     = session('cart', []);

        // Lọc và tính tổng
        $items = []; $grand = 0;
        foreach ($selected as $id) {
            if (isset($cart[$id])) {
                $items[$id] = $cart[$id];
                $grand    += $cart[$id]['price'] * $cart[$id]['quantity'];
            }
        }
        if (empty($items)) {
            return redirect()->route('cart.index')
                             ->with('error','Bạn chưa chọn sản phẩm nào để thanh toán.');
        }

        // Sinh mã CK duy nhất và lưu tạm
        $bankRef = $this->uniqueBankRef();
        session(['pending_bank_ref'=>$bankRef]);

        // Build QR tĩnh từ VietQR.io
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
     * Xử lý lưu đơn COD hoặc Bank
     */
    public function confirm(Request $r)
    {
        \Log::debug('⚡️ in confirm(), payload:', $r->all());
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

        $total = array_sum(array_map(fn($i)=> $i['price'] * $i['quantity'], $cart));

        // Lấy bank_ref từ hidden hoặc session
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

        // Lưu items
        foreach ($cart as $pid => $item) {
            $order->items()->create([
                'product_id' => $pid,
                'quantity'   => $item['quantity'],
                'price'      => $item['price'],
            ]);
        }

        // Xóa session
        session()->forget(['cart','pending_bank_ref']);

        return view('checkout.thankyou', compact('order'));
    }

    /**
     * POST /checkout/bank-ref
     * Sinh mã CK + QR SVG ngay khi khách chọn chuyển khoản.
     */
    public function ajaxBankRef(Request $r)
    {
        $r->validate(['amount'=>'required|integer']);
        $amount = $r->amount;
        $ref    = $this->uniqueBankRef();

        // Payload nội bộ (EMVCo kiểu đơn giản)
        $payload = $this->vietQRPayloadRaw($amount, $ref);
        $svg     = QrCode::size(260)->generate($payload);

        session(['pending_bank_ref'=>$ref]);

        return response()->json(['ref'=>$ref,'qr'=>$svg]);
    }

    // ========== Helpers ===========
    private function uniqueBankRef(): string
    {
        do {
            $ref = Str::upper(Str::random(10));
        } while (\App\Models\Order::where('bank_ref',$ref)->exists());
        return $ref;
    }

    private function vietQRPayloadRaw(int $amount, string $ref): string
    {
        $acc  = '19032724004016';
        $name = 'PHAN THAO NGUYEN';
        return "{$acc}|{$name}|{$amount}|{$ref}";
    }
}
