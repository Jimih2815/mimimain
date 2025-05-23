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
  @media (max-width: 760px) {
  /* === Global Overrides cho mọi trang trên điện thoại === */
  main {
    padding: 4rem 0 0 0 !important;
  }
  #mcFloatingPanel {
    display: none;
  }

  /* === How To Pay – Mobile styles === */
  /* Hero */
  .pay-hero {
    padding: 3rem 1rem;        /* giảm padding dọc cho vừa màn hình */
  }
  .pay-hero h1 {
    font-size: 1.8rem;         /* tiêu đề vừa mắt */
  }
  .pay-hero p.lead {
    font-size: .95rem;
    margin-top: .5rem;
  }

  /* Wrapper chung */
  .w-50 {
    width: 90% !important;     /* tận dụng không gian rộng hơn */
    margin: 0 auto;
  }

  /* Sections */
  .pay-section {
    padding: 1.5rem 0.5rem;
  }
  .pay-section h2 {
    font-size: 1.4rem;
    margin-bottom: .75rem;
  }
  .pay-section p,
  .pay-section li {
    font-size: .95rem;
    line-height: 1.6;
  }
  .pay-section ul,
  .pay-section ol {
    padding-left: 1.2rem;
    margin-top: .5rem;
  }
  .pay-section ul li,
  .pay-section ol li {
    margin-bottom: .5rem;
  }

  /* Liên kết và nút nhấn */
  .pay-section a {
    font-size: .95rem;
    text-decoration: underline;
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
