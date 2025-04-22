<footer class="bg-light pt-5">
  <div class="container">

    {{-- 1. Links + Social columns --}}
    <div class="row">
      <div class="col-md-3 mb-4">
        <h5>Resources</h5>
        <ul class="list-unstyled">
          <li><a href="#">Find A Store</a></li>
          <li><a href="#">Become A Member</a></li>
          <li><a href="#">Running Shoe Finder</a></li>
          <li><a href="#">Send Us Feedback</a></li>
        </ul>
      </div>
      <div class="col-md-3 mb-4">
        <h5>Help</h5>
        <ul class="list-unstyled">
          <li><a href="#">Get Help</a></li>
          <li><a href="#">Order Status</a></li>
          <li><a href="#">Delivery</a></li>
          <li><a href="#">Returns</a></li>
          <li><a href="#">Payment Options</a></li>
          <li><a href="#">Contact Us</a></li>
        </ul>
      </div>
      <div class="col-md-3 mb-4">
        <h5>Company</h5>
        <ul class="list-unstyled">
          <li><a href="#">About MimiMain</a></li>
          <li><a href="#">News</a></li>
          <li><a href="#">Careers</a></li>
          <li><a href="#">Investors</a></li>
          <li><a href="#">Sustainability</a></li>
          <li><a href="#">Report a Concern</a></li>
        </ul>
      </div>
      <div class="col-md-3 mb-4">
        <h5>Theo dõi chúng tôi</h5>
        <div class="d-flex">
          <a href="#" class="text-dark me-3"><i class="fab fa-facebook-f fa-lg"></i></a>
          <a href="#" class="text-dark me-3"><i class="fab fa-instagram fa-lg"></i></a>
          <a href="#" class="text-dark me-3"><i class="fab fa-twitter fa-lg"></i></a>
          <a href="#" class="text-dark me-3"><i class="fab fa-pinterest fa-lg"></i></a>
          <a href="#" class="text-dark me-3"><i class="fab fa-tiktok fa-lg"></i></a>
          <a href="#" class="text-dark"><i class="fab fa-youtube fa-lg"></i></a>
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
          Ngõ 2 Tân Mỹ, Mỹ Đình, Nam Từ Liêm, Hà Nội<br>
          Liên hệ: <strong>0354.235.669</strong>
        </p>
      </div>

      {{-- Map (≈67% width) --}}
      <div class="col-md-8 mb-4">
        <div class="map-container mb-2">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3723.034928716555!2d105.7753423154153!3d21.02158289258868!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab6f4c9e7a1f%3A0x123456789abcdef!2zVGllbSBob2EgTWltZQ!5e0!3m2!1svi!2s!4v1234567890123"
            frameborder="0"
            allowfullscreen
            loading="lazy"
          ></iframe>
        </div>
      </div>
    </div>


    <div class="text-center pb-3">
      &copy; {{ date('Y') }} MimiMain. All rights reserved.
    </div>

  </div>
</footer>
