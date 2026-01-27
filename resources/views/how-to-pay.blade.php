{{-- resources/views/how-to-pay.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
  /* Hero Section */
  .pay-hero {
     background: linear-gradient(135deg, #4ab3af, #81e6d9);
    color: #fff;
    text-align: center;
    padding: 6rem 1rem;
    margin-bottom: 2rem;
  }
  .pay-hero h1 {
    font-size: 2.75rem;
    font-weight: 700;
  }
  .pay-hero p.lead {
    font-size: 1.25rem;
    margin-top: 0.5rem;
  }

  /* Section Styling */
  .pay-section {
    padding: 3rem 1rem;
  }
  .pay-section h2 {
    color: #4ab3af;
    font-size: 1.75rem;
    margin-bottom: 1rem;
  }
  .pay-section p,
  .pay-section li {
    font-size: 1rem;
    line-height: 1.6;
    color: #333;
  }
  .pay-section ol,
  .pay-section ul {
    padding-left: 1.25rem;
  }
  .pay-section.bg-light {
    background: #f8f9fa;
  }
  /* =========================
   Mobile tweaks (<= 768px)
   Paste this at the END of your <style>
   ========================= */
@media (max-width: 768px) {

  /* Container width: w-50 quá hẹp trên mobile */
  .w-50 {
    width: 92% !important;
  }

  /* Hero: gọn lại, chữ vừa mắt */
  .pay-hero {
    padding: 3.9rem 1rem !important;
    margin-bottom: 1.25rem !important;
    margin-top: 4rem;
  }
  .pay-hero h1 {
    font-size: 1.85rem !important;
    line-height: 1.15;
    margin-bottom: 0.35rem;
  }
  .pay-hero p.lead {
    font-size: 0.98rem !important;
    line-height: 1.5;
    margin-top: 0.65rem !important;
    margin-bottom: 0;
  }

  /* Section spacing */
  .pay-section {
    padding: 1.5rem 0.85rem !important;
  }
  .pay-section h2 {
    font-size: 1.25rem !important;
    margin-bottom: 0.75rem !important;
    line-height: 1.25;
  }

  /* Text readability */
  .pay-section p,
  .pay-section li {
    font-size: 0.98rem !important;
    line-height: 1.65 !important;
  }

  /* Lists/ol: dễ đọc, đỡ chật */
  .pay-section ul,
  .pay-section ol {
    padding-left: 1.15rem !important;
    margin-bottom: 0;
  }
  .pay-section li {
    margin-bottom: 0.55rem;
  }

  /* bg-light section: bo góc như card */
  .pay-section.bg-light {
    border-radius: 16px;
  }

  /* Link: tránh tràn dòng */
  .pay-section a {
    word-break: break-word;
  }

  /* Text-center section spacing */
  .pay-section.text-center p {
    margin-bottom: 0.6rem;
  }
}

/* Extra small phones (<= 380px) */
@media (max-width: 380px) {
  .pay-section {
    padding: 1.35rem 0.75rem !important;
  }
  .pay-hero h1 {
    font-size: 1.7rem !important;
  }
}

</style>

<div class="pay-hero">
  <div class="container">
    <h1>Hướng Dẫn Thanh Toán</h1>
    <p class="lead">Nhanh chóng – Tiện lợi – An toàn với Mimi</p>
  </div>
</div>

<div class="w-50 mx-auto">
  <section class="pay-section">
    <h2>1. Chuẩn bị sản phẩm</h2>
    <p>Chọn các sản phẩm yêu thích, tùy chọn phân loại (màu sắc, kích cỡ), sau đó:</p>
    <ul>
      <li>Nhấn <strong>Thêm vào giỏ</strong> để tiếp tục mua sắm.</li>
      <li>Hoặc nhấn <strong>Mua ngay</strong> để chuyển thẳng đến trang thanh toán.</li>
    </ul>
  </section>

  <section class="pay-section bg-light">
    <h2>2. Kiểm tra giỏ hàng</h2>
    <p>Vào <a href="{{ route('cart.index') }}">Giỏ Hàng</a> trên thanh menu, kiểm tra lại sản phẩm và số lượng, sau đó nhấn <strong>Thanh Toán</strong>.</p>
  </section>

  <section class="pay-section">
    <h2>3. Điền thông tin giao hàng</h2>
    <p>Trong form thanh toán, vui lòng cung cấp đầy đủ:</p>
    <ol>
      <li>Họ tên người nhận.</li>
      <li>Số điện thoại liên hệ.</li>
      <li>Địa chỉ giao hàng chi tiết.</li>
      <li>Email (để nhận xác nhận đơn).</li>
      <li>Ghi chú (nếu có).</li>
    </ol>
  </section>

  <section class="pay-section bg-light">
    <h2>4. Chọn phương thức thanh toán</h2>
    <p>Mimi hỗ trợ hai hình thức:</p>
    <ul>
      <li><strong>COD:</strong> Thanh toán khi nhận hàng.</li>
      <li><strong>Chuyển khoản/QR Pay:</strong> Chuyển khoản ngân hàng, quét mã QR (Momo, ZaloPay).</li>
    </ul>
  </section>

  <section class="pay-section">
    <h2>5. Xác nhận và hoàn tất</h2>
    <p>Sau khi chọn thanh toán, nhấn <strong>Xác Nhận</strong>. Bạn sẽ nhận email/SMS xác nhận đơn hàng và hướng dẫn thanh toán (nếu chuyển khoản).</p>
  </section>

  <section class="pay-section bg-light text-center">
    <h2>Hỗ trợ khi cần</h2>
    <p>Nếu gặp khó khăn, vui lòng gọi hotline <strong>0354235669</strong> hoặc chat trực tuyến để được hỗ trợ ngay.</p>
  </section>
</div>
@endsection
