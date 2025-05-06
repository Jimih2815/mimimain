{{-- resources/views/tracking.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
  /* Hero Section */
  .tracking-hero {
    background: #4ab3af;
    color: #fff;
    text-align: center;
    padding: 6rem 1rem;
    margin-bottom: 2rem;
  }
  .tracking-hero h1 {
    font-size: 2.75rem;
    font-weight: 700;
  }
  .tracking-hero p.lead {
    font-size: 1.25rem;
    margin-top: 0.5rem;
  }

  /* Section Styling */
  .tracking-section {
    padding: 3rem 1rem;
  }
  .tracking-section h2 {
    color: #4ab3af;
    font-size: 1.75rem;
    margin-bottom: 1rem;
  }
  .tracking-section p,
  .tracking-section li {
    font-size: 1rem;
    line-height: 1.6;
    color: #333;
  }
  .tracking-section ul,
  .tracking-section ol {
    padding-left: 1.25rem;
  }
  .tracking-section.bg-light {
    background: #f8f9fa;
  }
</style>

<div class="tracking-hero">
  <div class="container">
    <h1>Hướng Dẫn Theo Dõi Đơn Hàng</h1>
    <p class="lead">Dễ dàng kiểm tra trạng thái vận chuyển ngay trên tài khoản Mimi của bạn</p>
  </div>
</div>

<div class="w-50 mx-auto">
  <section class="tracking-section">
    <h2>1. Đăng nhập</h2>
    <p>Truy cập <a href="{{ route('login') }}">Trang Đăng Nhập</a> và nhập thông tin tài khoản để bắt đầu.</p>
  </section>

  <section class="tracking-section bg-light">
    <h2>2. Vào Trang Cá Nhân</h2>
    <p>Sau khi đăng nhập thành công, chọn <strong>Trang Cá Nhân</strong> hoặc biểu tượng người dùng ở góc phải trên cùng.</p>
  </section>

  <section class="tracking-section">
    <h2>3. Chọn mục Đơn Hàng</h2>
    <p>Trong Trang Cá Nhân, tìm và nhấn vào mục <strong>Đơn Hàng</strong>. Tại đây sẽ liệt kê toàn bộ đơn bạn đã đặt.</p>
  </section>

  <section class="tracking-section bg-light">
    <h2>4. Xem Mã Vận Đơn</h2>
    <p>Mỗi đơn hàng có cột <strong>Mã vận đơn</strong>. Nhấn vào mã để xem chi tiết.</p>
  </section>

  <section class="tracking-section">
    <h2>5. Kiểm Tra Trạng Thái</h2>
    <ul>
      <li>Nhóm thông tin giao hàng: Đã tiếp nhận, Đang vận chuyển, Giao thành công.</li>
      <li>Thời gian và vị trí cập nhật theo mỗi mốc.</li>
    </ul>
  </section>

  <section class="tracking-section bg-light">
    <h2>6. Hỗ trợ khi có sự cố</h2>
    <p>Nếu đơn hàng không cập nhật hoặc gặp vấn đề, vui lòng liên hệ hotline: <strong>0354235669</strong> để được hỗ trợ ngay.</p>
  </section>

  <section class="tracking-section text-center">
    <h2>Thao tác nhanh</h2>
    <p>Bạn cũng có thể truy cập trực tiếp: <a href="{{ route('profile.edit') }}">Trang Cá Nhân</a> &gt; Đơn Hàng.</p>
  </section>
</div>
@endsection
