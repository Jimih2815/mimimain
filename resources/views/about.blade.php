{{-- resources/views/about.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
  /* Hero Section */
  .about-hero {
    position: relative;
    background: linear-gradient(135deg, #4ab3af, #81e6d9);
    color: #fff;
    padding: 8rem 0;
    text-align: center;
  }
  .about-hero::after {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: linear-gradient(135deg, #4ab3af, #81e6d9);
  }
  .about-hero .container {
    position: relative;
    z-index: 1;
  }
  .about-hero h1 {
    font-size: 3rem;
    font-weight: 700;
  }
  .about-hero p.lead {
    font-size: 1.25rem;
    margin-top: 1rem;
  }

  /* Section Titles */
  .section-title {
    color: #4ab3af;
    font-weight: 600;
    margin-bottom: 2rem;
  }

  /* Timeline */
  .timeline {
    position: relative;
    padding-left: 2rem;
  }
  .timeline::before {
    content: '';
    position: absolute;
    left: 1rem; top: 0; bottom: 0;
    width: 4px;
    background: #4ab3af;
  }
  .timeline-item {
    position: relative;
    margin-bottom: 2rem;
    padding-left: 1rem;
  }
  .timeline-item::before {
    content: '';
    position: absolute;
    left: -0.25rem; top: 0.2rem;
    width: 0.5rem; height: 0.5rem;
    background: #4ab3af;
    border-radius: 50%;
  }

  /* Core Values */
  .values-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
  }
  .value-item {
    text-align: center;
  }
  .value-item i {
    font-size: 2.5rem;
    color: #4ab3af;
    margin-bottom: 0.5rem;
  }
  .value-item h5 {
    font-weight: 600;
    margin-bottom: 0.5rem;
  }

  /* Team */
  .team-member img {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 50%;
    margin-bottom: 1rem;
  }
  /* =========================
   Mobile tweaks (<= 768px)
   Paste this at the END of your <style>
   ========================= */
@media (max-width: 768px) {

  /* Container width: w-70 đang hơi "đỏng đảnh" trên mobile */
  .w-70 {
    width: 92% !important;
  }

  /* Hero: giảm chiều cao, tránh "nuốt" màn hình */
  .about-hero {
    margin-top: 4rem;
    padding: 4.25rem 0 !important;
    background-position: center !important;
  }
  .about-hero::after {
    /* overlay nhẹ hơn để chữ đọc rõ mà không "phủ đậm" quá */
    opacity: 0.88;
  }
  .about-hero h1 {
    font-size: 2rem !important;
    line-height: 1.15;
    margin-bottom: 0.5rem;
  }
  .about-hero p.lead {
    font-size: 1rem !important;
    margin-top: 0.75rem !important;
    padding: 0 0.5rem;
  }

  /* Title & spacing */
  .section-title {
    font-size: 1.35rem;
    margin-bottom: 1.25rem !important;
  }
  section.mb-5 {
    margin-bottom: 2.25rem !important;
  }

  /* Paragraph readability */
  p {
    font-size: 0.98rem;
    line-height: 1.6;
  }
  .text-center.mx-auto[style*="max-width"] {
    max-width: 100% !important;
    padding: 0 0.25rem;
  }

  /* Values: ép 2 cột cho đẹp, icon nhỏ lại */
  .values-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 1rem !important;
  }
  .value-item {
    padding: 0.75rem 0.5rem;
    border-radius: 12px;
    background: rgba(74, 179, 175, 0.06);
  }
  .value-item i {
    font-size: 2rem !important;
    margin-bottom: 0.4rem !important;
  }
  .value-item h5 {
    font-size: 1rem;
    margin-bottom: 0.25rem !important;
  }
  .value-item p {
    font-size: 0.9rem;
    margin-bottom: 0;
  }

  /* Timeline: thu gọn line & dot cho gọn gàng */
  .timeline {
    padding-left: 1.25rem !important;
  }
  .timeline::before {
    left: 0.6rem !important;
    width: 3px !important;
  }
  .timeline-item {
    margin-bottom: 1.25rem !important;
    padding-left: 0.75rem !important;
  }
  .timeline-item::before {
    left: -0.35rem !important;
    width: 0.45rem !important;
    height: 0.45rem !important;
    top: 0.3rem !important;
  }
  .timeline-item h5 {
    font-size: 1rem;
    margin-bottom: 0.25rem;
  }
  .timeline-item p {
    margin-bottom: 0;
    font-size: 0.95rem;
  }

  /* Team: ảnh nhỏ lại + spacing hợp lý */
  .team-member img {
    width: 96px !important;
    height: 96px !important;
    margin-bottom: 0.6rem !important;
  }
  .team-member h5 {
    font-size: 1rem;
    margin-bottom: 0.15rem;
  }
  .team-member p {
    font-size: 0.9rem;
    margin-bottom: 0;
  }

  /* Statistics: cái đoạn này đang dùng d-flex + col-* hơi "lệch hệ" */
  section.text-center .d-flex.justify-content-center.align-items-center.gap-3 {
    flex-wrap: wrap !important;
    gap: 0.75rem !important;
  }
  section.text-center .d-flex > .col-6 {
    width: calc(50% - 0.5rem) !important;
    max-width: calc(50% - 0.5rem) !important;
    margin-bottom: 0.75rem !important;
    padding: 0.75rem 0.5rem;
    border-radius: 12px;
    background: rgba(0, 0, 0, 0.03);
  }
  section.text-center .d-flex h3 {
    font-size: 1.35rem;
    margin-bottom: 0.25rem;
  }
  section.text-center .d-flex p {
    font-size: 0.9rem;
    margin-bottom: 0;
  }

  /* CTA button full width cho mobile */
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
}

</style>

<div class="about-hero">
  <div class="container">
    <h1>Chúng tôi là Mimi</h1>
    <p class="lead">Mang đến món quà ấm áp – nơi tình thương bắt đầu</p>
  </div>
</div>

<div class="w-70 mx-auto mt-4 mb-5">
  {{-- 1. Giới thiệu ngắn --}}
  <section class="mb-5">
    <h2 class="section-title text-center">Về Chúng Tôi</h2>
    <p class="text-center mx-auto" style="max-width:800px;">
      Mimi ra đời từ đam mê kết nối cảm xúc qua quà tặng thủ công. Chúng tôi tin rằng mỗi sản phẩm đều chứa đựng câu chuyện và giá trị riêng, sứ mệnh của Mimi là giúp bạn gửi trao yêu thương một cách chân thành nhất.
    </p>
  </section>

  {{-- 2. Core Values --}}
  <section class="mb-5">
    <h2 class="section-title text-center">Giá Trị Cốt Lõi</h2>
    <div class="values-grid">
      <div class="value-item">
        <i class="bi bi-lightbulb-fill"></i>
        <h5>Sáng Tạo</h5>
        <p>Không ngừng đổi mới, thiết kế độc đáo.</p>
      </div>
      <div class="value-item">
        <i class="bi bi-award-fill"></i>
        <h5>Chất Lượng</h5>
        <p>Chất liệu cao cấp, kiểm định nghiêm ngặt.</p>
      </div>
      <div class="value-item">
        <i class="bi bi-people-fill"></i>
        <h5>Khách Hàng</h5>
        <p>Luôn đặt trải nghiệm và hài lòng lên hàng đầu.</p>
      </div>
      <div class="value-item">
        <i class="bi bi-heart-fill"></i>
        <h5>Tận Tâm</h5>
        <p>Hỗ trợ chu đáo, phục vụ tận tình.</p>
      </div>
    </div>
  </section>

  {{-- 3. Timeline --}}
  <section class="mb-5">
    <h2 class="section-title text-center">Hành Trình Phát Triển</h2>
    <div class="timeline mx-auto" style="max-width:700px;">
      <div class="timeline-item">
        <h5>Tháng 1/2023</h5>
        <p>Khởi nguồn ý tưởng và phát triển mẫu gấu bông đầu tiên.</p>
      </div>
      <div class="timeline-item">
        <h5>Tháng 6/2023</h5>
        <p>Ra mắt trang web Mimi phiên bản đầu, bán thành công 50 đơn.</p>
      </div>
      <div class="timeline-item">
        <h5>Tháng 12/2024</h5>
        <p>Mở rộng dịch vụ in ảnh đèn ngủ, 500+ khách hàng hài lòng.</p>
      </div>
    </div>
  </section>

  {{-- 4. Team --}}
  <section class="mb-5">
    <h2 class="section-title text-center">Đội Ngũ Chúng Tôi</h2>
    <div class="row justify-content-center g-4">
      @php
        $team = [
          ['name'=>'Trường Giang','role'=>'Founder','img'=>'/images/team/giang.jpg'],
          ['name'=>'Lan Anh','role'=>'Designer','img'=>'/images/team/lan.jpg'],
          ['name'=>'Minh Tuấn','role'=>'Developer','img'=>'/images/team/tuan.jpg'],
        ];
      @endphp

      @foreach($team as $member)
        <div class="col-6 col-md-4 text-center team-member">
          <img src="{{ $member['img'] }}" alt="{{ $member['name'] }}">
          <h5>{{ $member['name'] }}</h5>
          <p class="text-muted">{{ $member['role'] }}</p>
        </div>
      @endforeach
    </div>
  </section>

  {{-- 5. Statistics / Partners (Optional) --}}
  <section class="mb-5 text-center">
    <h2 class="section-title">Một Số Con Số Ấn Tượng</h2>
    <div class="d-flex justify-content-center align-items-center gap-3">
      <div class="col-6 col-md-3 mb-4">
        <h3 class="fw-bold">1K+</h3>
        <p>Khách hàng hài lòng</p>
      </div>
      <div class="col-6 col-md-3 mb-4">
        <h3 class="fw-bold">1000+</h3>
        <p>Đơn hàng mỗi tháng</p>
      </div>
      <div class="col-6 col-md-3 mb-4">
        <h3 class="fw-bold">4.9/5</h3>
        <p>Đánh giá Trustpilot</p>
      </div>
      <div class="col-6 col-md-3 mb-4">
        <h3 class="fw-bold">20+</h3>
        <p>Đối tác vận chuyển</p>
      </div>
    </div>
  </section>

  {{-- 6. CTA --}}
  <section class="text-center">
    <h2 class="section-title">Tham Gia Cùng Chúng Tôi</h2>
    <p>Khám phá bộ sưu tập và lan tỏa yêu thương với Mimi ngay hôm nay.</p>
    <a href="{{ route('products.index') }}" class="btn-mimi nut-xanh text-decoration-none">Khám Phá Cửa Hàng</a>
  </section>
</div>
@endsection
