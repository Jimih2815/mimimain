{{-- resources/views/partials/mobile-footer.blade.php --}}

<style>
/* ==== Mobile Footer Accordion ==== */
.mobile-footer {
  background: #fff;
  border-top: 1px solid #e5e5e5;
  font-size: .9rem;
}
.mobile-footer .mf-section {
  border-bottom: 1px solid #e5e5e5;
}
.mobile-footer .mf-heading {
  padding: 1rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  cursor: pointer;
  font-weight: 600;
}
.mobile-footer .mf-heading i {
  transition: transform .3s ease;
}
.mobile-footer .mf-heading i.rotate {
  transform: rotate(180deg);
}
.mobile-footer .mf-content {
  overflow: hidden;
  max-height: 0;
  transition: max-height .3s ease;
}
.mobile-footer .mf-content.expand {
  max-height: 500px; /* đủ cao để chứa nội dung */
}
.mobile-footer .mf-content ul {
  padding-left: 1.25rem;
  margin: 0 0 1rem;
}
.mobile-footer .mf-content li {
  padding: .25rem 0;
}
/* Theo dõi chúng tôi (không accordion) */
.mobile-footer .mf-social {
  padding: 1rem;
}
.mobile-footer .mf-social .icon {
  font-size: 1.5rem;
  margin-right: 1rem;
  color: #333;
}
/* Địa chỉ + Map */
.mobile-footer .mf-address,
.mobile-footer .mf-map {
  padding: 1rem;
}
.mobile-footer .mf-map iframe {
  width: 100%;
  height: 150px;
  border: 0;
}
a {
    text-decoration: none;
}
.logo-cont a {
  display: flex ; 
  justify-content: center;
  align-items: center;
}
.mobile-footer .list-unstyled a, .mobile-footer .mf-content li {
  color: white;
    -webkit-text-stroke: 1px #4ab3af;
    font-size: 1rem;
    font-weight: 200;
}
</style>

<div id="siteFooter" class="footer mobile-footer d-block d-lg-none mt-5">

  {{-- Giới thiệu --}}
  <div class="mf-section">
    <div class="mf-heading">
      <span>Giới thiệu</span>
      <i class="fa fa-chevron-down"></i>
    </div>
    <div class="mf-content">
      <ul class="list-unstyled">
        <li><a href="{{ route('about') }}">Về Mimi Shop</a></li>
        <li><a href="{{ route('vision-mission') }}">Tầm nhìn, sứ mệnh</a></li>
        <li><a href="{{ route('commitment') }}">Cam kết</a></li>
        <li><a href="{{ route('news.index') }}">Tin tức</a></li>
      </ul>
    </div>
  </div>

  {{-- Liên hệ --}}
  <div class="mf-section">
    <div class="mf-heading">
      <span>Liên hệ</span>
      <i class="fa fa-chevron-down"></i>
    </div>
    <div class="mf-content">
      <ul class="list-unstyled">
        <li><a href="https://maps.app.goo.gl/3V7A9WdkKkVS4x1X8" target="_blank">Địa chỉ</a></li>
        <li><a href="tel:0354235669">Hotline: 0354 235 669</a></li>
        <li><a href="mailto:youremail@example.com">Email</a></li>
        <li>9:00 - 17:30</li>
      </ul>
    </div>
  </div>

  {{-- Chính sách --}}
  <div class="mf-section">
    <div class="mf-heading">
      <span>Chính sách</span>
      <i class="fa fa-chevron-down"></i>
    </div>
    <div class="mf-content">
      <ul class="list-unstyled">
        <li><a href="{{ route('policy.return') }}">Đổi trả</a></li>
        <li><a href="{{ route('policy.warranty') }}">Bảo hành</a></li>
        <li><a href="{{ route('policy.shipping') }}">Vận chuyển</a></li>
        <li><a href="{{ route('policy.privacy') }}">Bảo mật thông tin</a></li>
      </ul>
    </div>
  </div>

  {{-- Hỗ trợ khách hàng --}}
  <div class="mf-section">
    <div class="mf-heading">
      <span>Hỗ trợ khách hàng</span>
      <i class="fa fa-chevron-down"></i>
    </div>
    <div class="mf-content">
      <ul class="list-unstyled">
        <li><a href="{{ route('faq') }}">FAQ</a></li>
        <li><a href="{{ route('how-to-order') }}">Hướng dẫn đặt hàng</a></li>
        <li><a href="{{ route('tracking') }}">Theo dõi đơn hàng</a></li>
        <li><a href="{{ route('how-to-pay') }}">Hướng dẫn thanh toán</a></li>
      </ul>
    </div>
  </div>

  {{-- Theo dõi chúng tôi --}}
  <div class="mf-section mf-social">
    <div class="mf-heading p-0" style="cursor:default">
      <span>Theo dõi chúng tôi</span>
    </div>
    <div class="d-flex pt-2 logo-cont">
      <a href="https://www.tiktok.com/@tiemhoamimi1" target="_blank" class="icon"><i class="fab fa-tiktok"></i></a>
      <a href="https://facebook.com/yourpage" target="_blank" class="icon"><i class="fab fa-facebook-f"></i></a>
      <a href="https://www.threads.net/@yourprofile" target="_blank" class="icon"><i class="fa-brands fa-threads"></i></a>
      <a href="https://zalo.me/0354235669" target="_blank" class="icon">
        <img src="/logochat/logo-zalo.png" alt="Zalo" style="width:1.5rem; height:1.5rem;">
      </a>
      <a href="https://shopee.vn/thanhhuyen2461" target="_blank" class="icon">
        <img src="/logochat/shopee.png" alt="Shopee" style="width:1.5rem; height:1.5rem;">
      </a>
    </div>
  </div>

  {{-- Địa chỉ --}}
  <div class="mf-address">
    <h6 class="mb-2">Địa chỉ</h6>
    <p class=" mb-0">
      37 TT11 Foresa 1A, KĐT Foresa Xuân Phương,<br>
      Nam Từ Liêm, Hà Nội<br>
      Liên hệ: <strong>0354.235.669</strong>
    </p>
  </div>

  {{-- Bản đồ --}}
  <div class="mf-map">
        <iframe src="https://www.google.com/maps/embed?pb=!1m13!1m8!1m3!1d14896.435121458197!2d105.7425!3d21.028333!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMjHCsDAxJzQyLjAiTiAxMDXCsDQ0JzMzLjAiRQ!5e0!3m2!1svi!2sus!4v1746517332466!5m2!1svi!2sus" 
          width="100%" height="150px" style="border:0;" 
          allowfullscreen="" loading="lazy" 
          referrerpolicy="no-referrer-when-downgrade"></iframe>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const sections = document.querySelectorAll('.mobile-footer .mf-section');
  sections.forEach(sec => {
    const heading = sec.querySelector('.mf-heading');
    const content = sec.querySelector('.mf-content');
    if (!content) return; // MF-social, Address, Map không có .mf-content

    heading.onclick = () => {
      const isExp = content.classList.toggle('expand');
      heading.querySelector('i').classList.toggle('rotate', isExp);
    };
  });
});
</script>
