<footer class="bg-light pt-5 an-des">
  <div class="container footer-container">

    {{-- 1. Links + Social columns --}}
    <div class="footer-danh-sach-cont">
      <div class="footer-danh-sach">
        <h5>Giới thiệu</h5>
        <ul class="list-unstyled">
          <li><a href="{{ route('about') }}">Về Mimi Shop</a></li>
          <li><a href="{{ route('vision-mission') }}">Tầm nhìn, sứ mệnh</a></li>
          <li><a href="{{ route('commitment') }}">Cam kết</a></li>
          <li><a href="{{ route('news.index') }}">Tin tức</a></li>
        </ul>
      </div>
      <div class="footer-danh-sach">
        <h5>Liên hệ</h5>
        <ul class="list-unstyled">
          <li><a href="https://maps.app.goo.gl/3V7A9WdkKkVS4x1X8" target="_blank">Địa chỉ</a></li>
          <li><a href="tel:0354235669">Hotline: 0354 235 669</a></li>
          <li><a href="#">Email</a></li>
          <li>9:00 - 17:30</li>
        </ul>
      </div>
      <div class="footer-danh-sach">
        <h5>Chính sách</h5>
        <ul class="list-unstyled">
          <li><a href="{{ route('policy.return') }}">Chính sách đổi trả</a></li>
          <li><a href="{{ route('policy.warranty') }}">Chính sách bảo hành</a></li>
          <li><a href="{{ route('policy.shipping') }}">Chính sách vận chuyển giao hàng</a></li>
          <li><a href="{{ route('policy.privacy') }}">Chính sách bảo mật thông tin</a></li>
        </ul>
      </div>
      <div class="footer-danh-sach">
        <h5>Hỗ trợ khách hàng</h5>
        <ul class="list-unstyled">
          <li><a href="{{ route('faq') }}">Câu hỏi thường gặp (FAQ)</a></li>
          <li><a href="{{ route('how-to-order') }}">Hướng dẫn đặt hàng</a></li>
          <li><a href="{{ route('tracking') }}">Theo dõi đơn hàng</a></li>
          <li><a href="{{ route('how-to-pay') }}">Hướng dẫn thanh toán</a></li>
        </ul>
      </div>
      <div class="footer-danh-sach">
        <h5>Theo dõi chúng tôi</h5>
        <div class="d-flex">
        
        <a href="https://www.tiktok.com/@tiemhoamimi1" target="_blank" class="text-dark me-3">
          <i class="fab fa-tiktok fa-lg"></i>
        </a>
        <a href="https://facebook.com/yourpage" target="_blank" class="text-dark me-3">
          <i class="fab fa-facebook-f fa-lg"></i>
        </a>          
        <a href="https://www.threads.net/@yourprofile" target="_blank" class="text-dark me-3">
          <i class="fa-brands fa-threads fa-lg"></i>
        </a>
          <a href="https://zalo.me/0354235669" target="_blank" class="text-dark me-3">
            <img src="/logochat/logo-zalo.png" alt="Zalo" style="width: 20px; margin-bottom: 0.4rem; height: 20px;">
          </a>
          <a href="https://shopee.vn/thanhhuyen2461" target="_blank" class="text-dark me-3">
            <img src="/logochat/shopee.png" alt="Zalo" style="width: 20px; margin-bottom: 0.4rem; height: 20px;">
          </a>

        </div>
      </div>
    </div>

    <hr>

    {{-- 2. Address + Map --}}
    <div class="row align-items-start">
      {{-- Địa chỉ (≈33% width) --}}
      <div class="col-md-4 mb-4">
        <h5>Địa chỉ</h5>
        <p>
          37 TT11 Foresa 1A, KĐT Foresa Xuân Phương, Nam Từ Liêm, Hà Nội<br>
          Liên hệ: <strong>0354.235.669</strong>
        </p>
      </div>

      {{-- Map (≈67% width) --}}
      <div class="col-md-8 mb-4">
        <div class="map-container mb-2">
        <iframe src="https://www.google.com/maps/embed?pb=!1m13!1m8!1m3!1d14896.435121458197!2d105.7425!3d21.028333!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMjHCsDAxJzQyLjAiTiAxMDXCsDQ0JzMzLjAiRQ!5e0!3m2!1svi!2sus!4v1746517332466!5m2!1svi!2sus" 
          width="100%" height="150px" style="border:0;" 
          allowfullscreen="" loading="lazy" 
          referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </div>

      
    </div>


    <div class="text-center pb-3">
      &copy; 2023 MimiShop. All rights reserved.
    </div>

  </div>
</footer>
