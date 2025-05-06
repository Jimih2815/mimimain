{{-- resources/views/return-policy.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
  /* Hero Section */
  .rp-hero {
    background: #4ab3af;
    color: #fff;
    text-align: center;
    padding: 5rem 1rem;
    margin-bottom: 2rem;
  }
  .rp-hero h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
  }
  .rp-hero p.lead {
    font-size: 1.1rem;
    margin: 0;
  }

  /* Section Styling */
  .rp-section {
    padding: 2.5rem 1rem;
  }
  .rp-section h2 {
    color: #4ab3af;
    font-size: 1.75rem;
    margin-bottom: 1rem;
  }
  .rp-section p,
  .rp-section li {
    font-size: 1rem;
    line-height: 1.6;
    color: #333;
  }
  .rp-section ul {
    padding-left: 1.25rem;
  }
</style>

<div class="rp-hero">
  <div class="container">
    <h1>Chính Sách Đổi Trả</h1>
    <p class="lead">Cam kết quyền lợi khách hàng – Đổi trả linh hoạt, thủ tục đơn giản</p>
  </div>
</div>

<div class="w-50 mx-auto">
  {{-- Điều kiện đổi trả --}}
  <section class="rp-section">
    <h2>1. Điều Kiện Đổi Trả</h2>
    <p>Khách hàng được quyền đổi/trả hàng trong vòng <strong>30 ngày</strong> kể từ ngày nhận sản phẩm, nếu thỏa mãn các điều kiện sau:</p>
    <ul>
      <li>Sản phẩm chưa qua sử dụng, còn nguyên bao bì, phụ kiện và tem nhãn.</li>
      <li>Sản phẩm lỗi kỹ thuật, không đúng mẫu mã hoặc hư hỏng do nhà sản xuất.</li>
      <li>Khách hàng cung cấp đầy đủ hóa đơn, biên nhận mua hàng.</li>
    </ul>
  </section>

  {{-- Thủ tục đổi trả --}}
  <section class="rp-section bg-light">
    <h2>2. Thủ Tục Đổi Trả</h2>
    <ol>
      <li>Liên hệ bộ phận Chăm sóc Khách hàng qua email: <a href="mailto:support@mimimain.com">support@mimi.com</a> hoặc số Hotline/Zalo: <strong>0354 235 669</strong>.</li>
      <li>Cung cấp thông tin đơn hàng, mã đơn và mô tả lý do đổi/trả kèm hình ảnh (nếu có).</li>
      <li>Xác nhận từ phía MimiMain trong vòng <strong>24 giờ</strong> kể từ khi nhận yêu cầu.</li>
      <li>Gửi trả sản phẩm về địa chỉ trung tâm bảo hành của MimiMain hoặc bưu cục gần nhất.</li>
      <li>Sau khi kiểm tra, chúng tôi sẽ tiến hành đổi sản phẩm mới hoặc hoàn tiền theo phương thức đã chọn.</li>
    </ol>
  </section>

  {{-- Thời gian giải quyết --}}
  <section class="rp-section">
    <h2>3. Thời Gian Xử Lý</h2>
    <p>Thời gian xử lý đổi/trả từ <strong>3-5 ngày</strong> làm việc (không tính Thứ 7, Chủ nhật và ngày lễ). Trường hợp cần kiểm định thêm, thời gian có thể kéo dài thêm tối đa 7 ngày.</p>
  </section>

  {{-- Phương thức hoàn tiền --}}
  <section class="rp-section bg-light">
    <h2>4. Phương Thức Hoàn Tiền</h2>
    <p>Khách hàng có thể lựa chọn 1 trong các hình thức:</p>
    <ul>
      <li>Hoàn tiền vào tài khoản ngân hàng và chuyển khoản trong vòng 3-5 ngày làm việc.</li>
      <li>Hoàn tiền qua ví điện tử (Momo, ZaloPay) trong vòng 24 giờ.</li>
      <li>Đổi sang voucher mua hàng có giá trị tương đương.</li>
    </ul>
  </section>

  {{-- Chi phí vận chuyển --}}
  <section class="rp-section">
    <h2>5. Chi Phí Vận Chuyển</h2>
    <p>MimiMain chịu trách nhiệm chi phí vận chuyển trong các trường hợp:</p>
    <ul>
      <li>Sản phẩm lỗi, hỏng do nhà sản xuất.</li>
      <li>Giao nhầm hàng so với đơn đặt.</li>
    </ul>
    <p>Trường hợp khách hàng đổi trả vì lý do cá nhân, chi phí vận chuyển sẽ do khách hàng tự thanh toán.</p>
  </section>

  {{-- Trường hợp không áp dụng --}}
  <section class="rp-section bg-light">
    <h2>6. Trường Hợp Không Áp Dụng</h2>
    <ul>
      <li>Sản phẩm bị hư hỏng do va đập, ngập nước, hoặc sử dụng sai hướng dẫn.</li>
      <li>Sản phẩm hết hạn bảo hành hoặc không còn tem niêm phong.</li>
      <li>Phiếu bảo hành/hóa đơn bị mất.</li>
    </ul>
  </section>

  {{-- Liên hệ --}}
  <section class="rp-section text-center">
    <h2>Liên Hệ Hỗ Trợ</h2>
    <p>Mọi thắc mắc về chính sách đổi trả, xin vui lòng liên hệ:</p>
    <p><strong>Email:</strong> <a href="mailto:support@mimimain.com">support@mimi.com</a> | <strong>Hotline:</strong> 0354 235 669</p>
  </section>
</div>
@endsection
