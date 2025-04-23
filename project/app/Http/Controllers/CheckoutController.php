<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    /**
     * Hiển thị trang Checkout và sinh QR chuyển khoản chuẩn EMVCo qua VietQR.io
     */
    public function show(Request $request)
    {
        $selected = $request->input('selected_ids', []);
        $cart     = session('cart', []);

        // Lọc những item đã chọn
        $items = [];
        $grand = 0;
        foreach ($selected as $id) {
            if (isset($cart[$id])) {
                $items[$id] = $cart[$id];
                $grand    += $cart[$id]['price'] * $cart[$id]['quantity'];
            }
        }

        if (empty($items)) {
            return redirect()->route('cart.index')
                             ->with('error', 'Bạn chưa chọn sản phẩm nào để thanh toán.');
        }

        // Thông tin Techcombank
        $bankCode    = 'TCB';                 // Mã ngân hàng Techcombank
        $accountNo   = '19032724004016';      // Số tài khoản
        $accountName = 'PHAN THAO NGUYEN';    // Chủ tài khoản
        $amount      = $grand;                // Số tiền nguyên

        // Sinh Quick‑Link QR chuẩn EMVCo
        $qrUrl = "https://img.vietqr.io/image/{$bankCode}-{$accountNo}-compact.png"
               . "?amount={$amount}"
               . "&addInfo=" . urlencode('Thanh toan don hang')
               . "&accountName=" . urlencode($accountName);

        return view('checkout.show', compact('items', 'grand', 'qrUrl'));
    }

    /**
     * Xử lý form Checkout (COD hoặc Bank)
     */
    public function process(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string',
            'phone'   => 'required|string',
            'address' => 'required|string',
            'note'    => 'nullable|string',
            'payment' => 'required|in:cod,bank',
        ]);

        // TODO: lưu đơn hàng vào DB, gửi mail/thông báo, v.v.

        if ($data['payment'] === 'bank') {
            $message = 'Cám ơn bạn! Chúng tôi đã nhận yêu cầu và sẽ kiểm tra giao dịch chuyển khoản.';
        } else {
            $message = 'Cám ơn bạn! Đơn hàng COD của bạn đang được xử lý.';
        }

        // Nếu muốn, xóa giỏ hàng đã thanh toán:
        // session()->forget('cart');

        return redirect()->route('products.index')
                         ->with('success', $message);
    }
}
