{{-- resources/views/shipping-policy.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
  /* Hero Section */
  .sp-hero {
    background: #4ab3af;
    color: #fff;
    text-align: center;
    padding: 6rem 1rem;
    margin-bottom: 2rem;
  }
  .sp-hero h1 {
    font-size: 2.75rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
  }
  .sp-hero p.lead {
    font-size: 1.25rem;
  }

  /* Section Styling */
  .sp-section {
    padding: 3rem 1rem;
  }
  .sp-section h2 {
    color: #4ab3af;
    font-size: 1.75rem;
    margin-bottom: 1rem;
  }
  .sp-section p,
  .sp-section li {
    font-size: 1rem;
    line-height: 1.6;
    color: #333;
  }
  .sp-section ul {
    padding-left: 1.25rem;
  }
  .sp-section.bg-light {
    background: #f8f9fa;
  }
</style>

<div class="sp-hero">
  <div class="container">
    <h1>Chính Sách Vận Chuyển & Giao Hàng</h1>
    <p class="lead">Nhanh chóng – An toàn – Phủ sóng toàn quốc</p>
  </div>
</div>

<div class="w-50 mx-auto">
  {{-- 1. Phạm vi áp dụng --}}
  <section class="sp-section">
    <h2>1. Phạm Vi Áp Dụng</h2>
    <p>Chính sách này áp dụng cho tất cả đơn hàng mua tại website Mimi.</p>
    <ul>
      <li>Giao hàng nội thành Hà Nội.</li>
      <li>Giao hàng đến tất cả tỉnh thành khác trên toàn quốc.</li>
      <li>Áp dụng cho cả đơn hàng mua lẻ và mua số lượng lớn.</li>
    </ul>
  </section>

  {{-- 2. Thời gian giao hàng --}}
  <section class="sp-section bg-light">
    <h2>2. Thời Gian Giao Hàng</h2>
    <ul>
      <li><strong>Nội thành:</strong> Giao hỏa tốc trong vòng 2 giờ kể từ khi xác nhận đơn.</li>
      <li><strong>Liên tỉnh:</strong> Thời gian 1-3 ngày làm việc tùy khu vực.</li>
      <li><strong>Miền núi, hải đảo:</strong> Thời gian 3-7 ngày làm việc.</li>
      <li>Không tính Thứ 7, Chủ Nhật và ngày lễ khi tính thời gian giao.</li>
    </ul>
  </section>

  {{-- 3. Đơn vị vận chuyển & Phương thức --}}
  <section class="sp-section">
    <h2>3. Đơn Vị Vận Chuyển & Phương Thức</h2>
    <p>Mimi hợp tác với các đơn vị uy tín:</p>
    <ul>
      <li>Giao hàng nhanh: GrabExpress, Ahamove, Bee.</li>
      <li>Giao hàng tiết kiệm: Giao Hàng Tiết Kiệm (GHTK), SPX, BEST.</li>
      <li>Bưu điện: VNPost, ViettelPost.</li>
    </ul>
    <p>Hỗ trợ nhận hàng và trả tiền (COD) hoặc thanh toán trước bằng chuyển khoản/QR.</p>
  </section>

  {{-- 4. Phí vận chuyển --}}
  <section class="sp-section bg-light">
    <h2>4. Phí Vận Chuyển</h2>
    <ul>
      <li><strong>Miễn phí giao hàng:</strong> Đơn hàng từ 200.000₫ trở lên.</li>
      <li><strong>Phí tiêu chuẩn:</strong> 25.000₫ cho hỏa tốc nội thành, 20.000₫ liên tỉnh.</li>
      <li><strong>Phụ phí:</strong> Khu vực vùng sâu, vùng xa có thể phát sinh thêm (tối đa 20.000₫).</li>
    </ul>
  </section>

  {{-- 5. Theo dõi đơn hàng --}}
  <section class="sp-section">
    <h2>5. Theo Dõi & Xác Nhận</h2>
    <p>Sau khi gởi, bạn sẽ nhận mã vận đơn trong phần Hồ Sơ của tài khoản để theo dõi trạng thái giao hàng trên website của MiMi hoặc đối tác vận chuyển.</p>
    <p>Để xác nhận giao thành công, nhân viên giao nhận sẽ yêu cầu ký nhận hoặc chụp ảnh sản phẩm giao.</p>
  </section>

  {{-- 6. Điều khoản & Lưu ý --}}
  <section class="sp-section bg-light">
    <h2>6. Điều Khoản & Lưu Ý</h2>
    <ul>
      <li>Vui lòng kiểm tra hàng trước khi nhận và ký xác nhận.</li>
      <li>Nếu hàng giao thiếu hoặc hư hỏng, từ chối nhận và chụp ảnh làm chứng, liên hệ ngay hotline để hỗ trợ.</li>
      <li>Thay đổi địa chỉ giao hàng vui lòng thông báo trước 1 ngày làm việc.</li>
      <li>Chúng tôi không chịu trách nhiệm cho các trường hợp do khách hàng cung cấp sai thông tin địa chỉ.</li>
    </ul>
  </section>

  {{-- 7. Liên hệ hỗ trợ --}}
  <section class="sp-section text-center">
    <h2>Liên Hệ Hỗ Trợ</h2>
    <p>Mọi thắc mắc về vận chuyển, vui lòng gọi hotline: <strong>0354 235 669</strong> hoặc email: <a href="mailto:support@mimi.com">support@mimi.com</a></p>
  </section>
</div>
@endsection
