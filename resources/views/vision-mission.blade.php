{{-- resources/views/vision-mission.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
  /* Hero Section */
  .vm-hero {
    background: linear-gradient(135deg, #4ab3af, #81e6d9);
    color: #fff;
    text-align: center;
    padding: 6rem 1rem;
    margin-bottom: 2rem;
  }
  .vm-hero h1 {
    font-size: 3rem;
    font-weight: 800;
  }
  .vm-hero p.lead {
    font-size: 1.25rem;
    margin-top: 1rem;
  }

  /* Section Styling */
  .vm-section {
    padding: 3rem 1rem;
  }
  .vm-section h2 {
    color: #4ab3af;
    font-size: 2rem;
    margin-bottom: 1rem;
  }
  .vm-section p {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #333;
  }

  /* Core Values Grid */
  .values-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
  }
  .value-item {
    text-align: center;
  }
  .value-item i {
    font-size: 2.5rem;
    color: #4ab3af;
    margin-bottom: 0.5rem;
  }
  /* =========================
   Mobile tweaks (<= 768px)
   Paste this at the END of your <style>
   ========================= */
@media (max-width: 767.98px) {

  /* Container width */
  .w-70 {
    width: 92% !important;
  }

  /* Hero: giảm chiều cao + chữ vừa mắt */
  .vm-hero {
    padding: 4.25rem 1rem !important;
    margin-bottom: 1.25rem !important;
    margin-top: 4rem;
  }
  .vm-hero h1 {
    font-size: 2rem !important;
    line-height: 1.15;
    margin-bottom: 0.5rem;
  }
  .vm-hero p.lead {
    font-size: 1rem !important;
    margin-top: 0.75rem !important;
    line-height: 1.5;
  }

  /* Section spacing */
  .vm-section {
    padding: 1.75rem 0.75rem !important;
  }
  .vm-section h2 {
    font-size: 1.4rem !important;
    margin-bottom: 0.75rem !important;
  }
  .vm-section p {
    font-size: 0.98rem !important;
    line-height: 1.65 !important;
  }

  /* Mission list: đỡ chật + dễ đọc */
  .vm-section ul {
    padding-left: 1.1rem;
    margin-top: 0.75rem;
    margin-bottom: 0;
  }
  .vm-section li {
    margin-bottom: 0.6rem;
    font-size: 0.95rem;
    line-height: 1.55;
  }
  .vm-section li strong {
    display: inline;
  }

  /* Values: ép 2 cột cho đẹp, thêm card nhẹ */
  .values-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 1rem !important;
    margin-top: 1.25rem !important;
  }
  .value-item {
    padding: 0.85rem 0.65rem;
    border-radius: 14px;
    background: rgba(74, 179, 175, 0.08);
    border: 1px solid rgba(74, 179, 175, 0.12);
  }
  .value-item i {
    font-size: 2rem !important;
    margin-bottom: 0.35rem !important;
  }
  .value-item h5 {
    font-size: 1rem;
    margin-bottom: 0.25rem;
    font-weight: 700;
  }
  .value-item p {
    font-size: 0.9rem !important;
    margin-bottom: 0;
    line-height: 1.45 !important;
  }

  /* CTA button full width */
  .btn-mimi.nut-xanh {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    max-width: 420px;
    padding: 0.85rem 1rem;
    border-radius: 999px;
    font-weight: 600;
  }

  /* Optional: làm section bg-light “mềm” hơn trên mobile */
  .vm-section.bg-light {
    border-radius: 16px;
  }
}

</style>

<div class="vm-hero">
  <div class="container">
    <h1>Tầm Nhìn & Sứ Mệnh</h1>
    <p class="lead">Định hình tương lai quà tặng cá nhân – kết nối yêu thương trong từng khoảnh khắc</p>
  </div>
</div>

<div class="w-70 mx-auto">
  {{-- Vision --}}
  <section class="vm-section">
    <h2>Tầm Nhìn (Vision)</h2>
    <p>Mimi hướng đến trở thành thương hiệu quà tặng cá nhân hóa hàng đầu Việt Nam, nơi mỗi sản phẩm không chỉ là món quà, mà còn là câu chuyện, là kỷ niệm, là tình cảm được gửi gắm đến người thân, bạn bè và người yêu thương.</p>
  </section>

  {{-- Mission --}}
  <section class="vm-section bg-light">
    <h2>Sứ Mệnh (Mission)</h2>
    <p>Chúng tôi cam kết mang đến trải nghiệm quà tặng hoàn hảo thông qua:</p>
    <ul>
      <li><strong>Chất lượng vượt trội:</strong> Tuyệt đối chọn lọc nguyên liệu và quy trình sản xuất tinh xảo.</li>
      <li><strong>Sáng tạo không ngừng:</strong> Luôn đổi mới thiết kế, cá nhân hóa theo phong cách riêng của khách hàng.</li>
      <li><strong>Phục vụ tận tâm:</strong> Đặt khách hàng làm trọng tâm, đồng hành từ ý tưởng đến tay người nhận.</li>
    </ul>
  </section>

  {{-- Core Values --}}
  <section class="vm-section">
    <h2>Giá Trị Cốt Lõi (Core Values)</h2>
    <div class="values-grid">
      <div class="value-item">
        <i class="bi bi-lightbulb-fill"></i>
        <h5>Sáng tạo</h5>
        <p>Luôn đổi mới ý tưởng, thiết kế độc đáo.</p>
      </div>
      <div class="value-item">
        <i class="bi bi-award-fill"></i>
        <h5>Chất lượng</h5>
        <p>Tiêu chuẩn cao nhất trong từng chi tiết.</p>
      </div>
      <div class="value-item">
        <i class="bi bi-people-fill"></i>
        <h5>Khách hàng</h5>
        <p>Đặt trải nghiệm và niềm vui của khách lên hàng đầu.</p>
      </div>
      <div class="value-item">
        <i class="bi bi-heart-fill"></i>
        <h5>Tận tâm</h5>
        <p>Chăm sóc chu đáo, phục vụ nhiệt thành.</p>
      </div>
    </div>
  </section>

  {{-- CTA Section --}}
  <section class="vm-section text-center">
    <h2>Khám Phá Thêm</h2>
    <p>Hãy để Mimi đồng hành cùng khoảnh khắc ý nghĩa của bạn.</p>
    <a href="{{ route('products.index') }}" class="btn-mimi nut-xanh text-decoration-none">Khám phá sản phẩm</a>
  </section>
</div>
@endsection
