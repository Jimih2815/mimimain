{{-- resources/views/products/show-mobile.blade.php --}}
@extends('layouts.app-mobile')

@section('title', $product->name)

@section('content')
<style>
  /* 0) Ngăn overflow toàn cục */
  .product-show-mobile {
    overflow-x: hidden;
  }

  /* 1) Fix swiper overflow */
  .slider-product,
  .slider-related {
    overflow: hidden;
  }
  .slider-product .swiper,
  .slider-related .swiper {
    width: 100%;
    overflow: visible;
  }

  /* 2) Style nút điều hướng ảnh chính */
  .slider-product .product-slider-prev,
  .slider-product .product-slider-next {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    background: rgba(0,0,0,0.3);
    border: none;
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
  }
  .slider-product .product-slider-prev { left: 0.5rem; }
  .slider-product .product-slider-next { right: 0.5rem; }

  /* 3) Pagination chấm tròn ảnh chính */
  .slider-product .swiper-pagination {
    position: absolute;
    bottom: 0.5rem;
    left: 50%;
    transform: translateX(-50%);
    z-index: 10;
  }
  .slider-product .swiper-pagination-bullet {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: rgba(255,255,255,0.7);
    margin: 0 4px !important;
    opacity: 1;
  }
  .slider-product .swiper-pagination-bullet-active {
    background: #4ab3af;
  }

  /* 4) Style nút điều hướng slider related */
  .slider-related .related-prev,
  .slider-related .related-next {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    background: rgba(0,0,0,0.3);
    border: none;
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
  }
  .slider-related .related-prev { left: 0; }
  .slider-related .related-next { right: 0; }

  /* 5) Fix mô tả TinyMCE không tràn */
  .product-long-description,
  .product-long-description * {
    max-width: 100%;
    box-sizing: border-box;
    word-break: break-word;
  }
  .product-long-description h1 {
    font-size: 1.5rem;
    margin: 1rem 0 0.5rem;
  }
  .product-long-description h2 {
    font-size: 1.25rem;
    margin: 0.75rem 0 0.5rem;
  }
  .product-long-description p,
  .product-long-description span {
    font-size: 1rem;
    line-height: 1.4;
  }
  .product-long-description img {
    max-width: 100%;
    height: auto;
    display: block;
    margin: 0.5rem 0;
  }
  .product-long-description p > img {
  display: inline-block;

}
.ten-mobile {
    display: none;
}
</style>

<div class="product-show-mobile">

  {{-- 1) Slider ảnh chính + ảnh phụ --}}
  @php
    $slides = [];
    if ($product->img) {
      $slides[] = asset('storage/'.$product->img);
    }
    $subImgs = is_string($product->sub_img)
             ? json_decode($product->sub_img, true) ?: []
             : (array)$product->sub_img;
    foreach ($subImgs as $path) {
      $slides[] = asset('storage/'.$path);
    }
  @endphp

  <div class="slider-product mb-3">
    <div class="swiper product-swiper">
      <div class="swiper-wrapper">
        @foreach ($slides as $url)
          <div class="swiper-slide">
            <img src="{{ $url }}" class="img-fluid w-100" alt="{{ $product->name }}">
          </div>
        @endforeach
      </div>
      <div class="swiper-pagination product-swiper-pagination"></div>
    </div>
    <!-- <button type="button" class="product-slider-prev">
      <i class="fa-solid fa-chevron-left text-white"></i>
    </button>
    <button type="button" class="product-slider-next">
      <i class="fa-solid fa-chevron-right text-white"></i>
    </button> -->
  </div>

  {{-- 2) Giá --}}
  <div class="mb-2">
    <p class="fs-4 mb-0">
      Giá: <strong id="total-price">{{ number_format($product->base_price,0,',','.') }}₫</strong>
    </p>
  </div>

  {{-- 3) Tên --}}
  <div class="mb-3">
    <h2 class="mb-0">{{ $product->name }}</h2>
  </div>

  {{-- 4) Form chọn tuỳ chọn & mua hàng --}}
  <form id="add-to-cart-form-mobile"
        action="{{ route('cart.add', $product->id) }}"
        method="POST">
    @csrf

    @forelse ($optionTypes as $type)
      <div class="mb-3">
        <label class="form-label">{{ $type->name }}</label>
        <div class="d-flex option-items-show mb-2" data-first-group="{{ $loop->first?1:0 }}">
          @foreach ($type->values as $val)
            <div class="option-item-show me-2 p-2 border rounded"
                 data-type-id="{{ $type->id }}"
                 data-val-id="{{ $val->id }}"
                 data-extra="{{ $val->extra_price }}"
                 data-img="{{ $val->option_img?asset('storage/'.$val->option_img):'' }}">
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
      {{-- Không có tuỳ chọn --}}
    @endforelse

    <div id="option-error"
         class="text-danger small mb-3"
         style="display:none;">
      Vui lòng chọn các tuỳ chọn.
    </div>

    <div class="d-flex gap-2 mb-4">
      <button type="submit" class="btn btn-success flex-fill">Thêm vào giỏ hàng</button>
      <button type="button"
              id="buy-now-btn-mobile"
              class="btn btn-warning flex-fill">Mua ngay</button>
    </div>
  </form>

  {{-- 5) Slider “Có thể bạn cũng thích” --}}
  <div class="mt-4">
    <h3 class="h5">Có thể bạn cũng thích</h3>
    <div class="slider-related mb-3">
      <div class="swiper product-swiper-related">
        <div class="swiper-wrapper">
          @foreach ($relatedProducts as $rel)
            <div class="swiper-slide">
              @include('partials.product-card', ['product' => $rel])
            </div>
          @endforeach
        </div>
      </div>
      <!-- <button type="button" class="related-prev">
        <i class="fa-solid fa-chevron-left text-white"></i>
      </button>
      <button type="button" class="related-next">
        <i class="fa-solid fa-chevron-right text-white"></i>
      </button> -->
    </div>
  </div>

  {{-- 6) Mô tả dài --}}
  @if ($product->long_description)
    <div class="product-long-description mb-4">
      {!! $product->long_description !!}
    </div>
  @endif

  {{-- 7) Mimi Cam kết --}}
  <div class="product-guarantees d-flex flex-wrap gap-3 mb-4">
    @foreach ([
      ['fa-truck-fast','Giao hàng toàn quốc — Nhanh chóng - An toàn - Đúng hẹn'],
      ['fa-arrow-rotate-left','Đổi trả miễn phí 7 ngày — Dù bất kỳ lý do gì'],
      ['fa-shield','Lỗi 1 đổi 1 trong vòng 7 ngày'],
      ['fa-credit-card','Thanh toán linh hoạt — Nhận hàng trả tiền hoặc chuyển khoản'],
      ['fa-headset','Hỗ trợ khách hàng 24/7 — Chat, gọi điện trực tiếp'],
      ['fa-gift','Ưu đãi quà tặng kèm đơn hàng mỗi tuần'],
      ['fa-leaf','Bao bì thân thiện môi trường — Tiêu dùng bền vững'],
      ['fa-plane','Ship nhanh 1-2 ngày toàn quốc'],
      ['fa-truck','Miễn phí vận chuyển từ 199.000₫'],
      ['fa-tags','Giảm 5% cho khách hàng thân thiết'],
      ['fa-bolt','Giao hỏa tốc Hà Nội trong vòng 2 giờ'],
    ] as $g)
      <div class="d-flex align-items-center">
        <i class="fas {{ $g[0] }} me-2"></i>
        <span>{{ $g[1] }}</span>
      </div>
    @endforeach
  </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // 1) Xử lý form chọn tùy chọn & cập nhật giá
  const basePrice = {{ $product->base_price }};
  const totalEl   = document.getElementById('total-price');
  const items     = document.querySelectorAll('.option-item-show');
  const selected  = {};
  const errorEl   = document.getElementById('option-error');
  const form      = document.getElementById('add-to-cart-form-mobile');
  const buyBtn    = document.getElementById('buy-now-btn-mobile');

  function updateTotal() {
    const sum = Object.values(selected).reduce((a, b) => a + b, 0);
    totalEl.textContent = (basePrice + sum).toLocaleString('vi-VN') + '₫';
  }

  items.forEach(el => {
    el.addEventListener('click', () => {
      const typeId = el.dataset.typeId;
      const extra  = parseInt(el.dataset.extra) || 0;
      document.querySelectorAll(`.option-item-show[data-type-id="${typeId}"]`)
        .forEach(x => x.classList.remove('selected'));
      el.classList.add('selected');
      selected[typeId] = extra;
      document.getElementById(`option-input-${typeId}`).value = el.dataset.valId;
      errorEl.style.display = 'none';
      updateTotal();
    });
  });

  buyBtn.addEventListener('click', () => {
    const missing = Array.from(form.querySelectorAll('input[id^="option-input-"]'))
                         .some(i => !i.value);
    if (missing) {
      errorEl.style.display = 'block';
      return;
    }
    form.action = "{{ route('checkout.buyNow', $product->id) }}";
    form.submit();
  });

  form.addEventListener('submit', e => {
    e.preventDefault();
    const missing = Array.from(form.querySelectorAll('input[id^="option-input-"]'))
                         .some(i => !i.value);
    if (missing) {
      errorEl.style.display = 'block';
      return;
    }
    fetch(form.action, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: new FormData(form),
    })
    .then(r => r.json())
    .then(json => {
      if (json.success) {
        const badge = document.getElementById('cart-count');
        if (badge) badge.textContent = json.total_items;
        const btn  = form.querySelector('button[type="submit"]');
        const orig = btn.textContent;
        btn.textContent = 'Đã thêm';
        setTimeout(() => btn.textContent = orig, 1500);
      } else {
        alert(json.message || 'Thêm thất bại');
      }
    });
  });
});
</script>
@endpush


