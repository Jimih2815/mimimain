{{-- resources/views/products/show.blade.php --}}
@extends('layouts.app')

@section('content')


{{-- Breadcrumb: Danh mục > (Sector > Collection) > Product --}}
@php
  $hasCol = isset($firstCollection);
  $hasSec = isset($sector);
@endphp

<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb bg-white px-3 py-2">
    {{-- 1) Luôn có Danh mục --}}
    <li class="breadcrumb-item">
      <a href="{{ route('sector.index') }}">Danh mục</a>
    </li>

    {{-- 2) Nếu có sector và collection thì show thêm --}}
    @if($hasCol && $hasSec)
      <li class="breadcrumb-item">
        <a href="{{ route('sector.show', $sector->slug) }}">
          {{ $sector->name }}
        </a>
      </li>
      <li class="breadcrumb-item">
        <a href="{{ route('collections.show', $firstCollection->slug) }}">
          {{ $firstCollection->name }}
        </a>
      </li>
    @endif

    {{-- 3) Luôn có tên sản phẩm --}}
    <li class="breadcrumb-item active" aria-current="page">
      {{ $product->name }}
    </li>
  </ol>
</nav>

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
      @php
        // Xác định đã favorite chưa: auth → DB, guest → session
        $isFav = auth()->check()
          ? auth()->user()->favorites->contains($product->id)
          : in_array($product->id, session('favorites', []));
      @endphp

      <div class="trai-tim-cont d-flex align-items-center justify-content-between gap-2">
        <h2 class="mb-0">{{ $product->name }}</h2>
        <button
          type="button"
          class="btn-favorite trai-tim"
          data-id="{{ $product->id }}"
        >
          <i class="{{ $isFav ? 'fas text-danger' : 'far text-muted' }} fa-heart"></i>
        </button>
      </div>

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
          <!-- <p class="text-warning">Chưa có tuỳ chọn.</p> -->
        @endforelse

          {{-- Thông báo lỗi chọn option --}}
          <div id="option-error" class="text-danger small mb-2" style="display:none; font-size:1.3rem;">
            !!!Vui lòng chọn các tuỳ chọn.
          </div>
          <div class="d-flex gap-2 nut-mua-ngay-cont">
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
          
        </form>
      </div>
    </div>
  </div>
</div>


{{-- ─── RELATED PRODUCTS SLIDER ─── --}}
<div class="slider-related mb-5 ms-3 me-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Phù hợp với bạn</h2>
    <div class="d-flex align-items-center">
      <button type="button" class="btn btn-outline-secondary me-2 related-prev">
        <i class="fa-solid fa-chevron-left fs-4"></i>
      </button>
      <button type="button" class="btn btn-outline-secondary related-next">
        <i class="fa-solid fa-chevron-right fs-4"></i>
      </button>
    </div>
  </div>

  <div class="swiper product-swiper-related">
    <div class="swiper-wrapper">
      @foreach($relatedProducts as $rel)
        <div class="swiper-slide">
          @include('partials.product-card', ['product' => $rel])
        </div>
      @endforeach
    </div>
    {{-- nếu cần pagination: --}}
    {{-- <div class="swiper-pagination"></div> --}}
  </div>
</div>
{{-- ───────────────────────────────── --}}

@if($product->long_description)
  <div class="product-long-description mt-4">
    {!! $product->long_description !!}
  </div>
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const csrf           = document.querySelector('meta[name="csrf-token"]').content;
  const mainImg        = document.getElementById('main-product-img');
  const thumbs         = document.querySelectorAll('.thumb-item');
  const thumbsContainer= document.querySelector('.thumbs-container');
  const basePrice      = {{ $product->base_price }};
  const totalEl        = document.getElementById('total-price');
  const items          = document.querySelectorAll('.option-item-show');
  const selected       = {};
  const form           = document.getElementById('add-to-cart-form');
  const errorEl        = document.getElementById('option-error');
  const buyNowBtn      = document.getElementById('buy-now-btn');
  const badge          = document.getElementById('cart-count');

  // ─── Thumbnails ───
  function adjustThumbsHeight() {
    if (thumbsContainer && mainImg) {
      thumbsContainer.style.height = mainImg.clientHeight + 'px';
    }
  }
  mainImg.addEventListener('load', adjustThumbsHeight);
  window.addEventListener('resize', adjustThumbsHeight);
  if (mainImg.complete) adjustThumbsHeight();
  function swapThumb(t) {
    thumbs.forEach(x=>x.classList.remove('selected'));
    t.classList.add('selected');
    mainImg.src = t.dataset.src;
    adjustThumbsHeight();
  }
  thumbs.forEach(t => {
    t.addEventListener('click', ()=>swapThumb(t));
    t.addEventListener('mouseenter', ()=>swapThumb(t));
  });

  // ─── Option selection & price update ───
  function updateTotal() {
    const sum = Object.values(selected).reduce((a,b)=>a+b,0);
    totalEl.textContent = (basePrice + sum).toLocaleString('vi-VN') + '₫';
  }
  items.forEach(el => {
    el.addEventListener('click', () => {
      const typeId = el.dataset.typeId;
      const valId  = el.dataset.valId;
      const extra  = parseInt(el.dataset.extra)||0;
      document.querySelectorAll(`.option-item-show[data-type-id="${typeId}"]`)
              .forEach(x=>x.classList.remove('selected'));
      el.classList.add('selected');
      selected[typeId] = extra;
      document.getElementById(`option-input-${typeId}`).value = valId;
      errorEl.style.display = 'none';
      updateTotal();

      // swap main image if first group
      const grp = el.closest('.option-items-show');
      if (grp.dataset.firstGroup==='1' && el.dataset.img) {
        mainImg.src = el.dataset.img;
        adjustThumbsHeight();
      }
    });
  });

  // ─── Buy Now ───
  buyNowBtn.addEventListener('click', () => {
    const missing = Array.from(form.querySelectorAll('input[id^="option-input-"]'))
                         .some(i=>!i.value);
    if (missing) {
      errorEl.style.display = 'block';
      return;
    }
    form.action = "{{ route('checkout.buyNow', $product->id) }}";
    form.submit();
  });

  // ─── AJAX add to cart ───
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    const missing = Array.from(form.querySelectorAll('input[id^="option-input-"]'))
                         .some(i=>!i.value);
    if (missing) {
      errorEl.style.display = 'block';
      return;
    }
    errorEl.style.display = 'none';

    const btn      = form.querySelector('button[type="submit"]');
    const origText = btn.textContent;
    const data     = new FormData(form);

    fetch(form.action, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrf,
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: data
    })
    .then(res => res.json())
    .then(json => {
      if (!json.success) {
        alert(json.message || 'Thêm thất bại, thử lại nhé!');
        return;
      }

      // update badge
      if (badge) {
        badge.textContent   = json.total_items;
        badge.style.display = 'block';
      }

      // update dropdown-cart
      const listUL = document.querySelector('.scrollable-cart ul#header-cart-list');
      if (listUL) {
        const emptyLi = listUL.querySelector('.empty-cart');
        if (emptyLi) emptyLi.remove();

        let existing = listUL.querySelector(`li[data-key="${json.item.key}"]`);
        if (existing) {
          const sm = existing.querySelector('small.text-muted');
          sm.textContent = `${json.item.price.toLocaleString('vi-VN')}₫ × ${json.item.quantity}`;
        } else {
          const li = document.createElement('li');
          li.className       = 'd-flex align-items-center mb-2';
          li.dataset.key     = json.item.key;
          li.innerHTML       = `
            <img src="${json.item.image}"
                 width="50" class="me-2 rounded"
                 alt="${json.item.name}">
            <div class="flex-grow-1">
              <div class="fw-semibold">${json.item.name}</div>
              <small class="text-muted">
                ${json.item.price.toLocaleString('vi-VN')}₫ × ${json.item.quantity}
              </small>
            </div>`;
          listUL.appendChild(li);
        }
      }

      // cập nhật nút them giỏ
      const viewLi = document.querySelector('.view-cart-li');
      if (viewLi) viewLi.style.display = 'block';
      
      // button feedback
      btn.textContent = 'Đã thêm';
      setTimeout(()=> btn.textContent = origText, 1500);
    })
    .catch(err => {
      console.error(err);
      alert('Có lỗi xảy ra, xem console nhé.');
    });
  });
  document.querySelectorAll('.btn-favorite').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;
      fetch(`/favorites/toggle/${id}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json'
        },
      })
      .then(res => res.json())
      .then(json => {
        const icon = btn.querySelector('i.fa-heart');
        if (json.added) {
          // vừa thêm favorites
          icon.classList.replace('far', 'fas');
        } else {
          // vừa bỏ favorites
          icon.classList.replace('fas', 'far');
        }
      })
      .catch(console.error);
    });
  });
});
</script>
@endpush



