{{-- resources/views/warranty-policy.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
  /* Hero Section */
  .wp-hero {
    background: linear-gradient(135deg, #4ab3af, #81e6d9);
    color: #fff;
    text-align: center;
    padding: 5rem 1rem;
    margin-bottom: 2rem;
  }
  .wp-hero h1 {
    font-size: 2.5rem;
    font-weight: 700;
  }
  .wp-hero p.lead {
    font-size: 1.1rem;
  }

  /* Section Styling */
  .wp-section {
    padding: 2.5rem 1rem;
  }
  .wp-section h2 {
    color: #4ab3af;
    font-size: 1.75rem;
    margin-bottom: 1rem;
  }
  .wp-section p,
  .wp-section li {
    font-size: 1rem;
    line-height: 1.6;
    color: #333;
  }
  .wp-section ul {
    padding-left: 1.25rem;
  }
  .wp-section ol {
    padding-left: 1.25rem;
  }
  @media (max-width: 760px) {
  /* === Global Overrides cho mọi trang trên điện thoại === */
  main {
    padding: 4rem 0 0 0 !important;
  }
  #mcFloatingPanel {
    display: none;
  }

  /* === Warranty Policy – Mobile styles === */
  /* Hero */
  .wp-hero {
    padding: 3rem 1rem;       /* giảm padding dọc */
  }
  .wp-hero h1 {
    font-size: 1.8rem;        /* chữ vừa đủ nổi bật */
  }
  .wp-hero p.lead {
    font-size: .95rem;
  }

  /* Wrapper chung */
  .w-50 {
    width: 90% !important;    /* tận dụng không gian */
    margin: 0 auto;
  }

  /* Sections */
  .wp-section {
    padding: 1.5rem 0.5rem;
  }
  .wp-section h2 {
    font-size: 1.4rem;
    margin-bottom: .75rem;
  }
  .wp-section p,
  .wp-section li {
    font-size: . Nine五rem;   /* ~0.95rem */
    line-height: 1.6;
  }
  .wp-section ul,
  .wp-section ol {
    padding-left: 1.2rem;
    margin-top: .5rem;
  }
  .wp-section ul li,
  .wp-section ol li {
    margin-bottom: .5rem;
  }
}

</style>

<div class="wp-hero">
  <div class="container">
    <h1>Chính Sách Bảo Hành</h1>
    <p class="lead">An tâm sử dụng – Bảo hành chuyên nghiệp tại Mimi</p>
  </div>
</div>

<div class="w-50 mx-auto">
  {{-- 1. Phạm vi bảo hành --}}
  <section class="wp-section">
    <h2>1. Phạm Vị Bảo Hành</h2>
    <p>Mimi bảo hành miễn phí các lỗi kỹ thuật và hư hỏng do nhà sản xuất trong thời gian quy định:</p>
    <ul>
      <li>Bảo hành sản phẩm đèn ngủ: 12 tháng kể từ ngày mua.</li>
      <li>Bảo hành gấu bông và quà tặng thú nhồi bông: 6 tháng.</li>
      <li>Bảo hành phụ kiện đi kèm: 6 tháng.</li>
    </ul>
  </section>

  {{-- 2. Điều kiện bảo hành --}}
  <section class="wp-section bg-light">
    <h2>2. Điều Kiện Áp Dụng</h2>
    <p>Sản phẩm được bảo hành khi đáp ứng đủ các điều kiện sau:</p>
    <ul>
      <li>Sản phẩm còn nguyên tem niêm phong, chưa qua tự ý sửa chữa.</li>
      <li>Lỗi phát sinh do quá trình sản xuất hoặc vật liệu.</li>
      <li>Có hóa đơn, phiếu bảo hành hoặc biên lai mua hàng.</li>
    </ul>
    <p><strong>Lưu ý:</strong> Không bảo hành các trường hợp hư hỏng do va đập, ngấm nước, sử dụng sai hướng dẫn hoặc hao mòn tự nhiên.</p>
  </section>

  {{-- 3. Thủ tục bảo hành --}}
  <section class="wp-section">
    <h2>3. Thủ Tục Bảo Hành</h2>
    <ol>
      <li>Liên hệ Trung tâm Chăm sóc Khách hàng qua email: <a href="mailto:service@MiMi.com">service@MiMi.com</a> hoặc Hotline/Zalo: <strong>0354 235 669</strong>.</li>
      <li>Cung cấp thông tin đơn hàng, mã sản phẩm, mô tả lỗi kèm hình ảnh minh họa.</li>
      <li>Nhận hướng dẫn gửi sản phẩm về Trung tâm Bảo hành MiMi hoặc bưu cục gần nhất.</li>
      <li>Kỹ thuật viên kiểm tra, xác nhận và tiến hành sửa chữa hoặc thay mới.</li>
      <li>Nhận lại sản phẩm và biên lai, đồng thời nhận phiếu xác nhận bảo hành.</li>
    </ol>
  </section>

  {{-- 4. Thời gian xử lý --}}
  <section class="wp-section bg-light">
    <h2>4. Thời Gian Xử Lý</h2>
    <p>Thời gian bảo hành xử lý:</p>
    <ul>
      <li>Kiểm tra và xác nhận yêu cầu trong vòng 1-2 ngày làm việc.</li>
      <li>Sửa chữa hoặc thay mới hoàn thiện trong vòng 5-7 ngày làm việc (không tính Thứ 7, Chủ nhật).</li>
      <li>Trong trường hợp khan hiếm linh kiện, thời gian có thể kéo dài tối đa 14 ngày, chúng tôi sẽ thông báo trước cho khách hàng.</li>
    </ul>
  </section>

  {{-- 5. Chi phí vận chuyển bảo hành --}}
  <section class="wp-section">
    <h2>5. Chi Phí Vận Chuyển</h2>
    <p>MiMi chịu chi phí vận chuyển bảo hành trong các trường hợp lỗi do nhà sản xuất. Với các trường hợp phát sinh do người dùng, khách hàng vui lòng chịu chi phí gửi trả.</p>
  </section>

  {{-- 6. Liên hệ hỗ trợ --}}
  <section class="wp-section text-center">
    <h2>Liên Hệ Hỗ Trợ</h2>
    <p>Mọi thắc mắc vui lòng liên hệ:</p>
    <p><strong>Email:</strong> <a href="mailto:service@MiMi.com">service@MiMi.com</a> | <strong>Hotline:</strong> 0354 235 669</p>
  </section>
</div>
@endsection
