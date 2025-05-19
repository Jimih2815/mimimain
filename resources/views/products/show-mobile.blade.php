{{-- resources/views/products/show-mobile.blade.php --}}
@extends('layouts.app-mobile')

@section('title', $product->name)

@section('content')
<style>
  /* 0) Ngăn overflow toàn cục */
  .product-show-mobile { overflow-x: hidden; }

  /* Slider overflow */
  .slider-product, .slider-related { overflow: hidden; }
  .slider-product .swiper, .slider-related .swiper { width: 100%; overflow: visible; }

  /* Panel slide-up */
  #mobile-cart-panel {
    position: fixed; bottom: 0; left: 0; width: 100%;
    background: #fff; box-shadow: 0 -2px 8px rgba(0,0,0,0.1);
    transform: translateY(100%); transition: transform 0.3s ease;
    z-index: 100; max-height: 80%; overflow-y: auto;
  }
  #mobile-cart-panel.open { transform: translateY(0); }
  #mobile-cart-panel .panel-header {
    padding: 1rem; border-bottom: 1px solid #eee;
    text-align: center; font-weight: 600;
  }
  #mobile-cart-panel .panel-content { padding: 1rem; }

  /* Sticky bottom bar */
  #mobile-cart-bar {
    position: fixed; bottom: 0; left: 0; width: 100%;
    background: #fff; border-top: 1px solid #ddd;
    display: flex; gap: 0.5rem; padding: 0.5rem; z-index: 99;
  }
  #mobile-cart-bar button { flex: 1; }

  /* Option items */
  .panel-content .option-item-show {
    flex: 1; border: 1px solid #ccc; padding: 0.5rem;
    text-align: center; border-radius: 0.25rem;
    display: flex; align-items: center; justify-content: center; gap: 0.5rem;
  }
  .panel-content .option-item-show:not(.selected) { background: #f8f8f8; }
  .panel-content .option-item-show.selected { border-color: #4ab3af; }
  .panel-content .option-item-show img { width: 24px; height: 24px; object-fit: cover; }
</style>

<div class="product-show-mobile px-3">
  {{-- 1) Slider ảnh chính + ảnh phụ --}}
  @php
    $slides = [];
    if($product->img) $slides[] = asset('storage/'.$product->img);
    $subImgs = is_string($product->sub_img)
             ? json_decode($product->sub_img,true) ?: []
             : (array)$product->sub_img;
    foreach($subImgs as $path) $slides[] = asset('storage/'.$path);
  @endphp
  <div class="slider-product mb-3">
    <div class="swiper product-swiper">
      <div class="swiper-wrapper">
        @foreach($slides as $url)
          <div class="swiper-slide"><img src="{{ $url }}" class="img-fluid w-100" alt=""></div>
        @endforeach
      </div>
      <div class="swiper-pagination product-swiper-pagination"></div>
    </div>
  </div>

  {{-- 2) Giá --}}
  <div class="mb-2">
    <p class="fs-4 mb-0">Giá: <strong id="total-price">{{ number_format($product->base_price,0,',','.') }}₫</strong></p>
  </div>

  {{-- 3) Tên --}}
  <div class="mb-3"><h2 class="mb-0">{{ $product->name }}</h2></div>

  {{-- 5) Slider “Có thể bạn cũng thích” --}}
  <div class="mt-4">
    <h3 class="h5">Có thể bạn cũng thích</h3>
    <div class="slider-related mb-3">
      <div class="swiper product-swiper-related">
        <div class="swiper-wrapper">
          @foreach($relatedProducts as $rel)
            <div class="swiper-slide">@include('partials.product-card',['product'=>$rel])</div>
          @endforeach
        </div>
      </div>
    </div>
  </div>

  {{-- 6) Mô tả dài --}}
  @if($product->long_description)
    <div class="product-long-description mb-4">{!! $product->long_description !!}</div>
  @endif

  {{-- 7) Mimi Cam kết --}}
  <div class="product-guarantees d-flex flex-wrap gap-3 mb-4">
    @foreach([
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
      <div class="d-flex align-items-center"><i class="fas {{ $g[0] }} me-2"></i><span>{{ $g[1] }}</span></div>
    @endforeach
  </div>
</div>

{{-- Slide-up options panel --}}
<div id="mobile-cart-panel">
    <div id="mobile-panel-header" class="panel-header d-flex align-items-center">
      {{-- Ảnh của giá trị đầu tiên đang chọn (mặc định là giá trị đầu tiên của option_type đầu) --}}
      <img
        id="panel-img"
        src="{{ asset('storage/'.$optionTypes->first()->values->first()->option_img) }}"
        class="me-3"
        style="width:48px;height:48px;object-fit:cover;"
        alt="Chọn thuộc tính">
      <div class="d-flex flex-column">
        <div id="panel-total-price" class="fw-bold">
          {{ number_format($product->base_price,0,',','.') }}₫
        </div>
        <p class="mb-1 small">Freeship đơn hàng 199.000₫</p>
        <div id="panel-selected-names" class="small text-muted">
          {{-- tên các option đã chọn, ví dụ: đèn tròn, xanh, 10 bông --}}
        </div>
      </div>
    </div>
  <div class="panel-content">
    <form id="add-to-cart-form-mobile" action="{{ route('cart.add',$product->id) }}" method="POST">
      @csrf
      @foreach($optionTypes as $type)
        @php $isFirstType = $loop->first; @endphp
        <div class="mb-3">
            <label class="form-label">{{ $type->name }}</label>
            <div class="d-flex flex-wrap mb-2">
            @foreach($type->values as $val)
                <div
                class="option-item-show me-2 p-2 border rounded"
                data-type-id="{{ $type->id }}"
                data-val-id="{{ $val->id }}"
                data-extra="{{ $val->extra_price }}"
                data-img="{{ $val->option_img ? asset('storage/'.$val->option_img) : '' }}"
                >
                {{-- Chỉ nhóm đầu tiên mới show ảnh --}}
                @if($isFirstType && $val->option_img)
                    <img
                    src="{{ asset('storage/'.$val->option_img) }}"
                    class="me-1"
                    style="width:24px;height:24px;object-fit:cover;"
                    alt=""
                    >
                @endif
                <span>{{ $val->value }}</span>
                </div>
            @endforeach
            </div>
            <input
            type="hidden"
            name="options[{{ $type->id }}]"
            id="option-input-{{ $type->id }}"
            required
            >
        </div>
        @endforeach

      <div id="option-error" class="text-danger small mb-3" style="display:none;">Vui lòng chọn đầy đủ thuộc tính.</div>
      <div class="d-flex gap-2 mb-4">
        <button type="submit" class="btn btn-success">Thêm vào giỏ hàng</button>
        <button type="button" id="buy-now-btn-mobile" class="btn btn-warning">Mua ngay</button>
      </div>
    </form>
  </div>
</div>

{{-- Sticky bottom bar --}}
<div id="mobile-cart-bar">
  <button id="open-cart-panel" class="btn btn-outline-primary">Chọn thuộc tính</button>
  <button onclick="document.getElementById('add-to-cart-form-mobile').submit()" class="btn btn-success">Thêm vào giỏ</button>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const panel = document.getElementById('mobile-cart-panel');
  document.getElementById('open-cart-panel').addEventListener('click', () => panel.classList.toggle('open'));
  
  const basePrice = {{ $product->base_price }};
  const totalEl   = document.getElementById('total-price');
  const panelImg  = document.getElementById('panel-img');
  const panelPrice= document.getElementById('panel-total-price');
  const panelNames= document.getElementById('panel-selected-names');
  const items     = panel.querySelectorAll('.option-item-show');
  const selected  = {};
  const errorEl   = document.getElementById('option-error');
  const form      = document.getElementById('add-to-cart-form-mobile');
  const buyBtn    = document.getElementById('buy-now-btn-mobile');


function renderPanelHeader() {
    // 1) tính tổng giá
    const sumExtra = Object.values(selected).reduce((a,b)=>a+b, 0);
    panelPrice.textContent = (basePrice + sumExtra).toLocaleString('vi-VN') + '₫';

    // 2) cập nhật tên các option đã chọn
    const names = [];
    panel.querySelectorAll('.option-item-show.selected').forEach(el => {
      names.push(el.textContent.trim());
    });
    panelNames.textContent = names.join(', ');
  }
  function updateTotal() {
    const sum = Object.values(selected).reduce((a,b)=>a+b,0);
    totalEl.textContent = (basePrice + sum).toLocaleString('vi-VN') + '₫';
  }
    items.forEach(el => {
    el.addEventListener('click', () => {
        const typeId = el.dataset.typeId;
        const extra  = parseInt(el.dataset.extra) || 0;

        // reset selection nhóm đó
        panel.querySelectorAll(`.option-item-show[data-type-id="${typeId}"]`)
            .forEach(x => x.classList.remove('selected'));
        el.classList.add('selected');

        // nếu đây là option_type đầu tiên, đổi ảnh
        const firstTypeId = '{{ $optionTypes->first()->id }}';
        if (typeId == firstTypeId) {
        const imgUrl = el.dataset.img;
        if (imgUrl) panelImg.src = imgUrl;
        }

        selected[typeId] = extra;
        document.getElementById(`option-input-${typeId}`).value = el.dataset.valId;
        errorEl.style.display = 'none';
        renderPanelHeader();
    });
    });


  buyBtn.addEventListener('click', () => {
    const missing = Array.from(form.querySelectorAll('input[id^="option-input-"]')).some(i=>!i.value);
    if(missing) return errorEl.style.display='block';
    form.action = "{{ route('checkout.buyNow',$product->id) }}";
    form.submit();
  });

  form.addEventListener('submit', e => {
    e.preventDefault();
    const missing = Array.from(form.querySelectorAll('input[id^="option-input-"]')).some(i=>!i.value);
    if(missing) return errorEl.style.display='block';
    fetch(form.action, {
      method:'POST',
      headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'X-Requested-With':'XMLHttpRequest'},
      body:new FormData(form)
    }).then(r=>r.json()).then(json=>{
      if(json.success) {
        document.getElementById('cart-count').textContent = json.total_items;
        const btn = form.querySelector('button[type="submit"]'); const orig = btn.textContent;
        btn.textContent='Đã thêm'; setTimeout(()=>btn.textContent=orig,1500);
        panel.classList.remove('open');
      } else alert(json.message||'Thêm thất bại');
    });
  });
});
</script>
@endpush
