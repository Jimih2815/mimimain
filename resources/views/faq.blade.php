{{-- resources/views/faq.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
  /* Hero Section */
  .faq-hero {
    background: #4ab3af;
    color: #fff;
    text-align: center;
    padding: 6rem 1rem;
    margin-bottom: 2rem;
  }
  .faq-hero h1 {
    font-size: 2.75rem;
    font-weight: 700;
  }
  .faq-hero p.lead {
    font-size: 1.25rem;
    margin-top: 0.5rem;
  }
</style>

<div class="faq-hero">
  <div class="container">
    <h1>Câu Hỏi Thường Gặp (FAQ)</h1>
    <p class="lead">Giải đáp nhanh mọi thắc mắc của bạn với Mimi</p>
  </div>
</div>

<div class="w-50 mx-auto mb-5">
  <div class="accordion" id="faqAccordion">
    <!-- 1 -->
    <div class="accordion-item">
      <h2 class="accordion-header" id="faqHeading1">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="true" aria-controls="faqCollapse1">
          Làm thế nào để đặt hàng trên Mimi?
        </button>
      </h2>
      <div id="faqCollapse1" class="accordion-collapse collapse show" aria-labelledby="faqHeading1" data-bs-parent="#faqAccordion">
        <div class="accordion-body">
          Chọn sản phẩm muốn mua, thêm vào giỏ hàng, sau đó vào trang Thanh Toán, điền đầy đủ thông tin giao hàng và phương thức thanh toán rồi nhấn Xác Nhận.
        </div>
      </div>
    </div>
    <!-- 2 -->
    <div class="accordion-item">
      <h2 class="accordion-header" id="faqHeading2">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
          Tôi có thể thanh toán bằng những phương thức nào?
        </button>
      </h2>
      <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#faqAccordion">
        <div class="accordion-body">
          Mimi hỗ trợ thanh toán: COD khi nhận hàng, chuyển khoản ngân hàng, QR Pay.
        </div>
      </div>
    </div>
    <!-- 3 -->
    <div class="accordion-item">
      <h2 class="accordion-header" id="faqHeading3">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
          Tôi có thể theo dõi tình trạng đơn hàng ở đâu?
        </button>
      </h2>
      <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#faqAccordion">
        <div class="accordion-body">
          Sau khi đặt hàng, bạn nhận Mã Vận Đơn qua email/SMS để theo dõi trực tiếp trên website vận chuyển. Ngoài ra, vào <a href="{{ route('profile.edit') }}">Trang Cá Nhân</a> cũng có mục Đơn Hàng để xem trạng thái.
        </div>
      </div>
    </div>
    <!-- 4 -->
    <div class="accordion-item">
      <h2 class="accordion-header" id="faqHeading4">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4">
          Thời gian giao hàng mất bao lâu?
        </button>
      </h2>
      <div id="faqCollapse4" class="accordion-collapse collapse" aria-labelledby="faqHeading4" data-bs-parent="#faqAccordion">
        <div class="accordion-body">
          Nội thành giao hỏa tốc trong 2 giờ; liên tỉnh 1-3 ngày; vùng sâu vùng xa 3-7 ngày (không tính thứ 7, CN và lễ).
        </div>
      </div>
    </div>
    <!-- 5 -->
    <div class="accordion-item">
      <h2 class="accordion-header" id="faqHeading5">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse5" aria-expanded="false" aria-controls="faqCollapse5">
          Làm sao để đổi trả hoặc bảo hành sản phẩm?
        </button>
      </h2>
      <div id="faqCollapse5" class="accordion-collapse collapse" aria-labelledby="faqHeading5" data-bs-parent="#faqAccordion">
        <div class="accordion-body">
          Vui lòng tham khảo <a href="{{ route('policy.return') }}">Chính Sách Đổi Trả</a> và <a href="{{ route('policy.warranty') }}">Chính Sách Bảo Hành</a> để biết điều kiện và thủ tục chi tiết.
        </div>
      </div>
    </div>
    <!-- 6 -->
    <div class="accordion-item">
      <h2 class="accordion-header" id="faqHeading6">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse6" aria-expanded="false" aria-controls="faqCollapse6">
          Làm thế nào để liên hệ hỗ trợ khách hàng?
        </button>
      </h2>
      <div id="faqCollapse6" class="accordion-collapse collapse" aria-labelledby="faqHeading6" data-bs-parent="#faqAccordion">
        <div class="accordion-body">
          Gọi hotline <strong>0354235669</strong> hoặc gửi email về <a href="mailto:support@mimi.com">support@mimi.com</a>. Ngoài ra, bạn có thể gửi yêu cầu tại <a href="{{ route('help.create') }}">Trang Trợ Giúp</a>.
        </div>
      </div>
    </div>
    <!-- 7 -->
    <div class="accordion-item">
      <h2 class="accordion-header" id="faqHeading7">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse7" aria-expanded="false" aria-controls="faqCollapse7">
          Mimi có chương trình khuyến mãi nào thường xuyên không?
        </button>
      </h2>
      <div id="faqCollapse7" class="accordion-collapse collapse" aria-labelledby="faqHeading7" data-bs-parent="#faqAccordion">
        <div class="accordion-body">
          Chúng tôi thường xuyên có khuyến mãi vào các dịp lễ, Tết và sự kiện đặc biệt. Đăng ký nhận email hoặc theo dõi fanpage để cập nhật ưu đãi sớm nhất.
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
