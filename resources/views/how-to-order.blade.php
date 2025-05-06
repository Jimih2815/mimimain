{{-- resources/views/how-to-order.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
  /* Hero Section */
  .ho-hero {
    background: #4ab3af;
    color: #fff;
    text-align: center;
    padding: 6rem 1rem;
    margin-bottom: 2rem;
  }
  .ho-hero h1 {
    font-size: 2.75rem;
    font-weight: 700;
  }
  .ho-hero p.lead {
    font-size: 1.25rem;
    margin-top: 0.5rem;
  }
  /* Section Styling */
  .ho-section {
    padding: 3rem 1rem;
  }
  .ho-section h2 {
    color: #4ab3af;
    font-size: 1.75rem;
    margin-bottom: 1rem;
  }
  .ho-section p,
  .ho-section li {
    font-size: 1rem;
    line-height: 1.6;
    color: #333;
  }
  .ho-section ul {
    padding-left: 1.25rem;
  }
  .ho-section.bg-light {
    background: #f8f9fa;
  }
</style>

<div class="ho-hero">
  <div class="container">
    <h1>Hướng Dẫn Đặt Hàng</h1>
    <p class="lead">Đơn giản – Nhanh chóng – Tiện lợi với Mimi</p>
  </div>
</div>

<div class="w-50 mx-auto">
  {{-- 1. Chọn sản phẩm --}}
  <section class="ho-section">
    <h2>1. Chọn sản phẩm</h2>
    <p>Truy cập danh mục hoặc tìm kiếm sản phẩm, chọn phân loại (màu sắc, kích cỡ...), sau đó:</p>
    <ul>
      <li>Nhấn <strong>Thêm vào giỏ</strong> để mua nhiều sản phẩm cùng lúc.</li>
      <li>Nhấn <strong>Mua ngay</strong> để chuyển trực tiếp đến trang thanh toán.</li>
    </ul>
  </section>

  {{-- 2. Sử dụng Yêu thích --}}
  <section class="ho-section bg-light">
    <h2>2. Sử dụng Yêu thích</h2>
    <p>Nếu bạn muốn lưu sản phẩm để xem sau, nhấn biểu tượng <i class="bi bi-heart"></i>. Vào <a href="{{ route('favorites.index') }}">Yêu thích</a> để chọn phân loại và thêm giỏ ngay tại đây.</p>
  </section>

  {{-- 3. Kiểm tra giỏ hàng --}}
  <section class="ho-section">
    <h2>3. Kiểm tra giỏ hàng</h2>
    <p>Nhấn vào biểu tượng giỏ hàng để xem các sản phẩm đã chọn. Tại đây bạn có thể điều chỉnh số lượng, xóa hoặc tiếp tục mua sắm.</p>
  </section>

  {{-- 4. Điền thông tin giao hàng --}}
  <section class="ho-section bg-light">
    <h2>4. Điền thông tin giao hàng</h2>
    <p>Trong trang thanh toán, vui lòng điền đầy đủ:</p>
    <ul>
      <li>Họ tên người nhận.</li>
      <li>Số điện thoại liên hệ.</li>
      <li>Địa chỉ giao hàng chi tiết.</li>
    </ul>
  </section>

  {{-- 5. Chọn phương thức thanh toán --}}
  <section class="ho-section">
    <h2>5. Chọn phương thức thanh toán</h2>
    <p>Mimi hỗ trợ các hình thức:</p>
    <ul>
      <li><strong>COD:</strong> Thanh toán khi nhận hàng.</li>
      <li><strong>Chuyển khoản ngân hàng / Ví điện tử:</strong> Ngân hàng, Momo, ZaloPay với mã QR.</li>
    </ul>
  </section>

  {{-- 6. Xác nhận đơn hàng --}}
  <section class="ho-section bg-light">
    <h2>6. Xác nhận đơn hàng</h2>
    <p>Sau khi hoàn tất, bạn sẽ nhận email và SMS xác nhận chứa mã đơn hàng và thông tin vận chuyển.</p>
  </section>

  {{-- 7. Theo dõi & hỗ trợ --}}
  <section class="ho-section text-center">
    <h2>7. Theo dõi & Hỗ trợ</h2>
    <p>Vào <a href="{{ route('profile.edit') }}">Trang Cá Nhân</a> hoặc <a href="{{ route('faq') }}">FAQ</a> để theo dõi đơn hàng và nhận trợ giúp.</p>
    <p>Hotline hỗ trợ: <strong>0354235669</strong></p>
  </section>
</div>
@endsection
