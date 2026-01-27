{{-- resources/views/commitment.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
  /* Hero Section */
  .cm-hero {
    background: linear-gradient(135deg, #4ab3af, #81e6d9);
    color: #fff;
    text-align: center;
    padding: 6rem 1rem;
    margin-bottom: 2rem;
  }
  .cm-hero h1 {
    font-size: 3rem;
    font-weight: 800;
  }
  .cm-hero p.lead {
    font-size: 1.25rem;
    margin-top: 1rem;
  }

  /* Section Styling */
  .cm-section {
    padding: 3rem 1rem;
  }
  .cm-section h2 {
    color: #4ab3af;
    font-size: 2rem;
    margin-bottom: 1rem;
  }
  .cm-section p {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #333;
  }

  /* Commitment Grid */
  .commitment-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
  }
  .commitment-item {
    text-align: center;
  }
  .commitment-item i {
    font-size: 2.5rem;
    color: #4ab3af;
    margin-bottom: 0.5rem;
  }
  .commitment-item h5 {
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
    font-weight: 600;
  }
  .commitment-item p {
    font-size: 1rem;
    color: #555;
  }
  /* =========================
   Mobile tweaks (<= 768px)
   Paste this at the END of your <style>
   ========================= */
@media (max-width: 768px) {

  /* Container width */
  .w-70 {
    width: 92% !important;
  }

  /* Hero: bớt cao + chữ vừa mắt */
  .cm-hero {
    margin-top: 4rem;
    padding: 4.25rem 1rem !important;
    margin-bottom: 1.25rem !important;

  }
  .cm-hero h1 {
    font-size: 2rem !important;
    line-height: 1.15;
    margin-bottom: 0.5rem;
  }
  .cm-hero p.lead {
    font-size: 1rem !important;
    margin-top: 0.75rem !important;
    line-height: 1.5;
  }

  /* Section spacing */
  .cm-section {
    padding: 1.75rem 0.75rem !important;
  }
  .cm-section h2 {
    font-size: 1.4rem !important;
    margin-bottom: 0.75rem !important;
  }
  .cm-section p {
    font-size: 0.98rem !important;
    line-height: 1.65 !important;
  }

  /* Grid: ép 2 cột cho mobile, thêm card nhẹ */
  .commitment-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 0.9rem !important;
    margin-top: 1.25rem !important;
  }

  .commitment-item {
    padding: 0.95rem 0.75rem;
    border-radius: 14px;
    background: rgba(74, 179, 175, 0.08);
    border: 1px solid rgba(74, 179, 175, 0.12);
    text-align: center;
  }

  .commitment-item i {
    font-size: 2rem !important;
    margin-bottom: 0.35rem !important;
    display: inline-block;
  }

  .commitment-item h5 {
    font-size: 1rem;
    font-weight: 700;
    margin-top: 0.25rem !important;
    margin-bottom: 0.35rem !important;
    line-height: 1.25;
  }

  .commitment-item p {
    font-size: 0.9rem !important;
    line-height: 1.45 !important;
    margin-bottom: 0;
    color: #444 !important;
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

  /* Optional: bg-light section bo góc cho đẹp */
  .cm-section.bg-light {
    border-radius: 16px;
  }
}

/* =========================
   Extra small phones (<= 380px)
   Nếu máy bé quá thì cho grid về 1 cột cho thoáng
   ========================= */
@media (max-width: 380px) {
  .commitment-grid {
    grid-template-columns: 1fr !important;
  }
}

</style>

<div class="cm-hero">
  <div class="container">
    <h1>Cam kết của MimiMain</h1>
    <p class="lead">Chất lượng – An toàn – Tận tâm trong từng sản phẩm</p>
  </div>
</div>

<div class="w-70 mx-auto">
  {{-- Quality Commitment --}}
  <section class="cm-section">
    <h2>Cam kết Chất Lượng</h2>
    <p>Mỗi sản phẩm của MimiMain đều trải qua quy trình kiểm định nghiêm ngặt, từ khâu chọn nguyên liệu đến khâu thành phẩm, nhằm đảm bảo chất lượng vượt trội và độ bền lâu dài.</p>
  </section>

  {{-- Safety & Environment --}}
  <section class="cm-section bg-light">
    <h2>Cam kết An Toàn & Bền Vững</h2>
    <p>Chúng tôi ưu tiên sử dụng nguyên liệu an toàn cho sức khỏe, giảm thiểu tác động đến môi trường và hướng đến quy trình sản xuất xanh, nhằm góp phần bảo vệ hành tinh của chúng ta.</p>
  </section>

  {{-- Core Commitments Grid --}}
  <section class="cm-section">
    <h2>Giá Trị Cam Kết</h2>
    <div class="commitment-grid">
      <div class="commitment-item">
        <i class="bi bi-award-fill"></i>
        <h5>Tiêu Chuẩn Cao</h5>
        <p>Cam kết đạt chứng nhận chất lượng quốc tế và vượt qua mọi tiêu chuẩn kiểm định.</p>
      </div>
      <div class="commitment-item">
        <i class="bi bi-heart-fill"></i>
        <h5>Tận Tâm Phục Vụ</h5>
        <p>Đội ngũ chăm sóc khách hàng luôn sẵn sàng hỗ trợ, đảm bảo bạn hài lòng tuyệt đối.</p>
      </div>
      <div class="commitment-item">
        <i class="bi bi-tree-fill"></i>
        <h5>Bảo Vệ Môi Trường</h5>
        <p>Ứng dụng quy trình sản xuất xanh, giảm thiểu rác thải và sử dụng vật liệu tái chế.</p>
      </div>
      <div class="commitment-item">
        <i class="bi bi-shield-lock-fill"></i>
        <h5>An Toàn Tuyệt Đối</h5>
        <p>Đảm bảo an toàn trong từng sản phẩm, không sử dụng chất độc hại, an toàn cho mọi đối tượng.</p>
      </div>
      <div class="commitment-item">
        <i class="bi bi-headset"></i>
        <h5>Hỗ Trợ Chu Đáo</h5>
        <p>Đội ngũ hỗ trợ 24/7, sẵn sàng giải đáp mọi thắc mắc và tư vấn tận tình.</p>
      </div>
      <div class="commitment-item">
        <i class="bi bi-tools"></i>
        <h5>Bảo Hành Linh Hoạt</h5>
        <p>Chính sách bảo hành đa dạng, linh hoạt phù hợp với mọi nhu cầu và sản phẩm.</p>
      </div>
      <div class="commitment-item">
        <i class="bi bi-arrow-repeat"></i>
        <h5>Đổi Trả Dễ Dàng</h5>
        <p>Cho phép đổi trả với mọi lý do trong vòng 30 ngày, thủ tục đơn giản và nhanh chóng.</p>
      </div>
      <div class="commitment-item">
      <i class="bi bi-send-fill"></i>  
        <h5>Vận Chuyển Nhanh Chóng</h5>
        <p>Giao hàng toàn quốc trong thời gian sớm nhất, tận tay bạn.</p>
      </div>
      <div class="commitment-item">
      <i class="bi bi-lightning-fill"></i>
        <h5>Ship Hỏa Tốc</h5>
        <p>Giao nội thành trong vòng 2 giờ, đảm bảo nhanh chóng.</p>
      </div>
      <div class="commitment-item">
        <i class="bi bi-arrow-clockwise"></i>
        <h5>Đổi Ngay Trong 1 Ngày</h5>
        <p>Nếu sản phẩm lỗi, chúng tôi đổi mới trong vòng 1 ngày.</p>
      </div>
    </div>
  </section>

  {{-- CTA Section --}}
  <section class="cm-section text-center">
    <h2>Đặt trọn niềm tin vào MimiMain</h2>
    <p>Hãy để chúng tôi đồng hành cùng khoảnh khắc ý nghĩa của bạn với cam kết chất lượng và dịch vụ hàng đầu.</p>
    <a href="{{ route('products.index') }}" class="btn-mimi nut-xanh text-decoration-none">Khám phá ngay</a>
  </section>
</div>
@endsection