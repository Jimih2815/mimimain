{{-- resources/views/products/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="flex-can-giua">
  <div class="chi-tiet-san-pham-cont">

    <div class="thong-tin-chi-tiet-cont">
      {{-- Cột thumbnails --}}
      @php
        // Lấy sub_img: nếu lưu dưới dạng JSON string, decode; nếu đã là array thì dùng luôn
        $subImgs = $product->sub_img;
        if (is_string($subImgs)) {
          $subImgs = json_decode($subImgs, true) ?: [];
        }
        $hasThumbs = !empty($subImgs);
      @endphp

      @if($hasThumbs)
        <div class="cot-anh-phu pe-2">
          <div class="thumbs-container">
            @foreach($subImgs as $idx => $path)
              <img
                src="{{ asset('storage/'.$path) }}"
                class="thumb-item {{ $idx === 0 ? 'selected' : '' }}"
                data-src="{{ asset('storage/'.$path) }}"
                alt="Thumb {{ $idx + 1 }}">
            @endforeach
          </div>
        </div>
      @endif

      {{-- Cột ảnh chính --}}
      <div class=" main-img-container">
        <img
          id="main-product-img"
          src="{{ asset('storage/'.$product->img) }}"
          class="img-fluid"
          alt="{{ $product->name }}">
      </div>

      {{-- Thông tin & chọn Option --}}
    <div class="option-san-pham">
      <h2>{{ $product->name }}</h2>
      <p class="text-muted">{{ $product->description }}</p>

      <p class="fs-4">
        Price:
        <strong id="total-price">{{ number_format($product->base_price,0,',','.') }}₫</strong>
      </p>

      <form action="{{ route('cart.add', $product->id) }}"
            method="POST"
            id="add-to-cart-form">
        @csrf

        {{-- Loop qua từng OptionType --}}
        @forelse($optionTypes as $type)
          <div class="mb-4">
            <label class="form-label">{{ $type->name }}</label>
            <div class="d-flex flex-row option-items-show mb-2"
                 data-first-group="{{ $loop->first ? 1 : 0 }}">
              @foreach($type->values as $val)
                <div class="option-item-show"
                     data-type-id="{{ $type->id }}"
                     data-val-id="{{ $val->id }}"
                     data-extra="{{ $val->extra_price }}"
                     data-img="{{ $val->option_img ? asset('storage/'.$val->option_img) : '' }}">
                  {{ $val->value }}
                </div>
              @endforeach
            </div>
            <input type="hidden"
                   name="options[{{ $type->id }}]"
                   id="option-input-{{ $type->id }}"
                   required>
          </div>
        @empty
          <p class="text-warning">Chưa có tuỳ chọn.</p>
        @endforelse
          <h3 class="h3-cam-ket">MiMi Cam Kết</h3>
          <div class="product-guarantees d-flex flex-wrap gap-3 mt-4">
            <div class="guarantee-item d-flex align-items-center">
              <i class="fas fa-truck-fast guarantee-icon"></i>
              <span>Giao hàng toàn quốc — Nhanh chóng - An toàn - Đúng hẹn</span>
            </div>
            <div class="guarantee-item d-flex align-items-center">
              <i class="fas fa-arrow-rotate-left guarantee-icon"></i>
              <span>Đổi trả miễn phí 7 ngày — Dù bất kỳ lý do gì</span>
            </div>
            <div class="guarantee-item d-flex align-items-center">
              <i class="fas fa-shield guarantee-icon"></i>
              <span>Lỗi 1 đổi 1 trong vòng 7 ngày</span>
            </div>
            
            <div class="guarantee-item d-flex align-items-center">
              <i class="fas fa-credit-card guarantee-icon"></i>
              <span>Thanh toán linh hoạt — Nhận hàng trả tiền hoặc chuyển khoản</span>
            </div>
            <div class="guarantee-item d-flex align-items-center">
              <i class="fas fa-headset guarantee-icon"></i>
              <span>Hỗ trợ khách hàng 24/7 — Chat, gọi điện trực tiếp</span>
            </div>
            <div class="guarantee-item d-flex align-items-center">
              <i class="fas fa-gift guarantee-icon"></i>
              <span>Ưu đãi quà tặng kèm đơn hàng mỗi tuần</span>
            </div>
            <div class="guarantee-item d-flex align-items-center">
              <i class="fas fa-leaf guarantee-icon"></i>
              <span>Bao bì thân thiện môi trường — Hướng tới tiêu dùng bền vững</span>
            </div>
            <div class="guarantee-item d-flex align-items-center">
              <i class="fas fa-star guarantee-icon"></i>
              <span>Đánh giá 5⭐ từ 95% khách hàng — Hơn 10.000 lượt mua</span>
            </div>
            <div class="guarantee-item d-flex align-items-center">
              <i class="fas fa-plane guarantee-icon"></i>
              <span>Ship nhanh 1-2 ngày toàn quốc</span>
            </div>
            <div class="guarantee-item d-flex align-items-center">
              <i class="fas fa-truck guarantee-icon"></i>
              <span>Miễn phí vận chuyển với đơn hàng từ 199.000₫</span>
            </div>

            <div class="guarantee-item d-flex align-items-center">
              <i class="fas fa-tags guarantee-icon"></i>
              <span>Giảm giá 5% cho khách hàng thân thiết</span>
            </div>

            <div class="guarantee-item d-flex align-items-center">
              <i class="fas fa-bolt guarantee-icon"></i>
              <span>Giao hàng hỏa tốc Hà Nội trong vòng 2 giờ</span>
            </div>
          </div>
          {{-- Thông báo lỗi chọn option --}}
          <div id="option-error" class="text-danger small mb-2" style="display:none;">
            Vui lòng chọn các tuỳ chọn.
          </div>
          <div class="d-flex gap-2">
            <!-- Nút Thêm vào giỏ hàng -->
            <button type="submit"
                    class="btn-them-gio-trang-chi-tiet">
              Thêm vào giỏ hàng
            </button>

            <!-- Nút Mua Ngay -->
            <button type="button"
                    id="buy-now-btn"
                    class="btn-mua-ngay">
              Mua ngay
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // --- Khởi tạo ảnh chính và thumbnails ---
  const mainImg         = document.getElementById('main-product-img');
  const thumbs          = document.querySelectorAll('.thumb-item');
  const thumbsContainer = document.querySelector('.thumbs-container');

  function adjustThumbsHeight() {
    if (thumbsContainer && mainImg) {
      thumbsContainer.style.height = mainImg.clientHeight + 'px';
    }
  }
  mainImg.addEventListener('load', adjustThumbsHeight);
  window.addEventListener('resize', adjustThumbsHeight);
  if (mainImg.complete) adjustThumbsHeight();

  function swapThumb(thumb) {
    thumbs.forEach(t => t.classList.remove('selected'));
    thumb.classList.add('selected');
    mainImg.src = thumb.dataset.src;
    adjustThumbsHeight();
  }
  thumbs.forEach(thumb => {
    thumb.addEventListener('click', () => swapThumb(thumb));
    thumb.addEventListener('mouseenter', () => swapThumb(thumb));
  });

  // --- Logic chọn Option và cập nhật giá ---
  const basePrice = {{ $product->base_price }};
  const totalEl   = document.getElementById('total-price');
  const items     = document.querySelectorAll('.option-item-show');
  const selected  = {};

  const form      = document.getElementById('add-to-cart-form');
  const errorEl   = document.getElementById('option-error');
  const buyNowBtn = document.getElementById('buy-now-btn');

  form.addEventListener('submit', e => {
    const missing = Array
      .from(form.querySelectorAll('input[id^="option-input-"]'))
      .some(i => !i.value);
    if (missing) {
      e.preventDefault();
      errorEl.style.display = 'block';
    }
  });

  function updateTotal() {
    const sumExtra = Object.values(selected).reduce((a, b) => a + b, 0);
    const total    = basePrice + sumExtra;
    totalEl.textContent = total.toLocaleString('vi-VN') + '₫';
  }

  items.forEach(el => {
    el.addEventListener('click', () => {
      const typeId = el.dataset.typeId;
      const valId  = el.dataset.valId;
      const extra  = parseInt(el.dataset.extra) || 0;

      // Chọn option
      document
        .querySelectorAll(`.option-item-show[data-type-id="${typeId}"]`)
        .forEach(sib => sib.classList.remove('selected'));
      el.classList.add('selected');

      selected[typeId] = extra;
      document.getElementById(`option-input-${typeId}`).value = valId;
      errorEl.style.display = 'none';
      updateTotal();

      // Đổi ảnh nếu nhóm đầu
      const groupEl = el.closest('.option-items-show');
      if (groupEl && groupEl.dataset.firstGroup === '1' && el.dataset.img) {
        mainImg.src = el.dataset.img;
        adjustThumbsHeight();
      }
    });
  });

  // --- Nút Mua Ngay ---
  buyNowBtn.addEventListener('click', () => {
    const missing = Array
      .from(form.querySelectorAll('input[id^="option-input-"]'))
      .some(i => !i.value);
    if (missing) {
      errorEl.style.display = 'block';
      return;
    }
    form.action = "{{ route('checkout.buyNow', $product->id) }}";
    form.submit();
  });
});
</script>
@endpush


