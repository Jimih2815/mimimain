{{-- resources/views/privacy-policy.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
  /* Hero Section */
  .pp-hero {
    background: #4ab3af;
    color: #fff;
    text-align: center;
    padding: 6rem 1rem;
    margin-bottom: 2rem;
  }
  .pp-hero h1 {
    font-size: 2.75rem;
    font-weight: 700;
  }
  .pp-hero p.lead {
    font-size: 1.25rem;
    margin-top: 0.5rem;
  }

  /* Section Styling */
  .pp-section {
    padding: 3rem 1rem;
  }
  .pp-section h2 {
    color: #4ab3af;
    font-size: 1.75rem;
    margin-bottom: 1rem;
  }
  .pp-section p,
  .pp-section li {
    font-size: 1rem;
    line-height: 1.6;
    color: #333;
  }
  .pp-section ul {
    padding-left: 1.25rem;
  }
  .pp-section.bg-light {
    background: #f8f9fa;
  }
</style>

<div class="pp-hero">
  <div class="container">
    <h1>Chính Sách Bảo Mật Thông Tin</h1>
    <p class="lead">Cam kết bảo vệ quyền riêng tư và an toàn dữ liệu khách hàng</p>
  </div>
</div>

<div class="w-50 mx-auto">
  {{-- 1. Mục đích và phạm vi thu thập --}}
  <section class="pp-section">
    <h2>1. Mục Đích & Phạm Vi Thu Thập</h2>
    <p>Mimi thu thập thông tin cá nhân để:</p>
    <ul>
      <li>Xử lý đơn hàng và giao hàng chính xác.</li>
      <li>Cải thiện chất lượng dịch vụ và chăm sóc khách hàng.</li>
      <li>Gửi thông tin khuyến mãi, ưu đãi (nếu khách hàng đồng ý).</li>
    </ul>
    <p>Thông tin thu thập bao gồm: họ tên, địa chỉ, số điện thoại, email, và các dữ liệu liên quan đến đơn hàng.</p>
  </section>

  {{-- 2. Phương thức thu thập --}}
  <section class="pp-section bg-light">
    <h2>2. Phương Thức Thu Thập</h2>
    <p>Chúng tôi thu thập thông tin khi:</p>
    <ul>
      <li>Khách hàng đăng ký tài khoản hoặc đặt hàng trên website.</li>
      <li>Khách hàng liên hệ qua form liên hệ, email hoặc hotline.</li>
      <li>Sử dụng cookies và công cụ phân tích để cải thiện trải nghiệm.</li>
    </ul>
  </section>

  {{-- 3. Thời gian lưu trữ --}}
  <section class="pp-section">
    <h2>3. Thời Gian Lưu Trữ</h2>
    <p>Thông tin của khách hàng được lưu trữ trong suốt thời gian khách hàng còn sử dụng dịch vụ và tối đa 24 tháng kể từ lần cuối tương tác.</p>
  </section>

  {{-- 4. Bảo mật thông tin --}}
  <section class="pp-section bg-light">
    <h2>4. Bảo Mật Thông Tin</h2>
    <p>Mimi cam kết áp dụng các biện pháp kỹ thuật và quản lý để bảo vệ thông tin khách hàng:</p>
    <ul>
      <li>Mã hóa dữ liệu qua SSL khi truyền và lưu trữ an toàn.</li>
      <li>Phân quyền truy cập chặt chẽ, chỉ nhân sự được ủy quyền mới xem được dữ liệu.</li>
      <li>Kiểm tra, cập nhật hệ thống định kỳ để ngăn chặn rủi ro bảo mật.</li>
    </ul>
  </section>

  {{-- 5. Sử dụng cookies --}}
  <section class="pp-section">
    <h2>5. Sử Dụng Cookies</h2>
    <p>Cookies giúp Mimi ghi nhớ thông tin và cải thiện trải nghiệm mua sắm:</p>
    <ul>
      <li>Cookies phiên (session cookies) để duy trì trạng thái đăng nhập.</li>
      <li>Cookies vĩnh viễn (persistent cookies) để lưu thông tin lựa chọn và cart.</li>
      <li>Khách hàng có thể tắt cookies trong trình duyệt, tuy nhiên sẽ ảnh hưởng trải nghiệm.</li>
    </ul>
  </section>

  {{-- 6. Quyền của khách hàng --}}
  <section class="pp-section bg-light">
    <h2>6. Quyền Của Khách Hàng</h2>
    <p>Khách hàng có quyền:</p>
    <ul>
      <li>Truy cập, chỉnh sửa hoặc yêu cầu xóa thông tin cá nhân.</li>
      <li>Rút lại quyền đồng ý nhận email khuyến mãi bất cứ lúc nào.</li>
      <li>Liên hệ và khiếu nại nếu thông tin bị sử dụng sai mục đích.</li>
    </ul>
  </section>

  {{-- 7. Liên hệ hỗ trợ --}}
  <section class="pp-section text-center">
    <h2>Liên Hệ Hỗ Trợ</h2>
    <p>Mọi thắc mắc về chính sách bảo mật, vui lòng gọi hotline: <strong>0354 235 669</strong></p>
    <p>hoặc email: <a href="mailto:privacy@mimi.com">privacy@mimi.com</a></p>
  </section>
</div>
@endsection
