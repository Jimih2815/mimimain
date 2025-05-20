{{-- resources/views/products/show-mobile.blade.php --}}
@extends('layouts.app-mobile')

@section('title', $product->name)

@section('content')
<style>
  /* Khi panel mở, khóa scroll cho body */
  body.no-scroll { overflow: hidden; }

  /* 0) Ngăn overflow toàn cục */
  .product-show-mobile { overflow-x: hidden; }

  /* Slider overflow */
  .slider-product, .slider-related { overflow: hidden; }
  .slider-product .swiper,
  .slider-related .swiper { width: 100%; overflow: visible; }

  /* Overlay fullscreen */
  #mobile-overlay {
    display: none;
    position: fixed; top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.3);
    z-index: 98;
  }
  #mobile-overlay.open { display: block; }

  /* ===== Slide-up cart panel ===== */
  #mobile-cart-panel{
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    max-height: 80vh;                   /* Giới hạn 80% màn hình */
    background:#fff;
    box-shadow:0 -2px 8px rgba(0,0,0,.1);
    transform:translateY(100%);
    transition:transform .3s ease;
    z-index:100;
    display:flex;                       /* Flex cột */
    flex-direction:column;
  }
  #mobile-cart-panel.open{ transform:translateY(0); }

  /* Header – dính mép trên */
  #mobile-panel-header{
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:1rem;
    border-bottom:1px solid #eee;
    flex:0 0 auto;                      /* Không cuộn */
  }
  #mobile-panel-header img{
    width:80px;
    aspect-ratio:1/1;
    object-fit:cover;
    margin-right:.75rem;
  }
  #mobile-panel-header .header-info{
    flex:1;
    display:flex;
    flex-direction:column;
    gap:.25rem;
    height:80px;
  }
  #mobile-panel-header button{
    background:transparent;
    border:none;
    font-size:1.5rem;
    line-height:1;
    padding:0;
  }

  /* Nội dung – cuộn khi dài */
    #mobile-cart-panel .panel-content{
    flex:1 1 auto;          /* đã có */
    min-height:0;           /* thêm để Safari/Chrome mobile tuân thủ */
    overflow-y:auto;        /* cuộn dọc */
  }
  /* Footer – dính mép dưới */
  #mobile-cart-panel .panel-footer{
    flex:0 0 auto;                      /* Không cuộn */
    display:flex;
    gap:.5rem;
    padding:.75rem;
    border-top:1px solid #eee;
    background:#fff;
  }

  /* ===== Sticky bottom bar ngoài panel ===== */
  #mobile-cart-bar{
    position:sticky;
    bottom:0;
    left:0;
    width:100%;
    background:#fff;
    border-top:1px solid #ddd;
    display:flex;
    gap:.5rem;
    padding:.5rem 1rem;
    z-index: 1;
    justify-content:space-between;
  }

  /* Option items */
  .panel-content .option-item-show{
    border:1px solid #4ab3af !important;
    padding:.5rem;
    display:flex;
    align-items:center;
    justify-content:center;
    gap:.5rem;
    border-radius:.25rem;
    width: auto;
    margin-top:.5rem;
    margin-left: 0.15rem;
    padding: 0.5rem;
    background-color: transparent !important;
  }
  .panel-content .option-item-show.selected   { 
    border-color:#4ab3af !important; 
    background-color: #4ab3af !important;
    color: white;
  }
  .panel-content .option-item-show img {
      width: 40px;
      aspect-ratio: 1 / 1;
      object-fit: cover;
  }

  #option-thumb-bar{ overflow:hidden; }
  #option-thumb-bar .thumb-scroll{
    overflow-x:auto;
    -ms-overflow-style:none;
    scrollbar-width:none;
  }
  #option-thumb-bar .thumb-scroll::-webkit-scrollbar{ display:none; }

  /* ===== Card & misc ===== */
  .ten-mobile{ display:none; }

  .card-img,
  .card-img-top,
  .card-img-bottom{
    width:100%;
    height:auto;
    aspect-ratio:1/1;
    object-fit:cover;
    border-radius:0;
  }
  .card-body{ padding:.3rem 0 !important; }
  .card{
    border-radius:0 !important;
    border:0;
    background-color:transparent;
  }

  .product-long-description span{
    display:block;
    white-space:normal;
    overflow-wrap:break-word;
  }

  .fa-heart{ font-size:1.5rem; }

  .swiper-pagination-bullet-active{
    color:#4ab3af !important;
    background:#4ab3af !important;
  }

  #total-price{ color:#fe3b27; }
  /* Form bao nội dung & footer – co giãn trong panel */
  #add-to-cart-form-mobile{
    display:flex;           /* biến form thành flex-column */
    flex-direction:column;
    flex:1 1 auto;          /* chiếm phần còn lại của panel */
    min-height:0;           /* <— chìa khóa: cho phép co */
  }

  .icon-xanh{ color:#4ab3af; font-size:1.3rem; }

  .border-top-icon{
    border-top:1px solid #4ab3af;
    padding-top:.3rem;
  }
  .border-bot-icon{
    border-bottom:1px solid #4ab3af;
    padding-bottom:.3rem;
  }

  .guarantee-item{
    padding:.5rem 0;
    border-bottom:1px dashed #ccc;
  }
  .guarantee-item:last-child{ border-bottom:none; }

  #img-zoom-overlay{
    position:fixed; inset:0;                /* phủ full viewport */
    display:none; align-items:center; justify-content:center;
    background:rgba(0,0,0,0);              /* sẽ fade-in */
    z-index:999;                            /* cao hẳn */
    animation:overlayFadeOut .35s forwards; /* default */
  }
  #img-zoom-overlay.open{                  /* khi mở */
    display:flex;
    animation:overlayFadeIn .35s forwards;
  }

  /* Ảnh bên trong overlay */
  #img-zoom{
    max-width:90vw; max-height:90vh;
    object-fit:contain;
    animation:zoomOut .35s forwards;        /* default (thu nhỏ) */
  }
  #img-zoom-overlay.open #img-zoom{        /* khi mở */
    animation:zoomIn .35s forwards;
  }

  /* ===== Keyframes ===== */
  @keyframes overlayFadeIn  { from{background:rgba(0,0,0,0);}   to{background:rgba(0,0,0,.9);} }
  @keyframes overlayFadeOut { from{background:rgba(0,0,0,.9);}  to{background:rgba(0,0,0,0);}  }
  @keyframes zoomIn  { from{transform:scale(.3); opacity:.2;} to{transform:scale(1); opacity:1;} }
  @keyframes zoomOut { from{transform:scale(1);  opacity:1;}  to{transform:scale(.3); opacity:0;} }

  #img-zoom-close{
    position:absolute; top:1rem; right:1rem;
    background:transparent; border:none;
    color:#fff; font-size:2rem; line-height:1;
  }

/* ==== Hiệu ứng ảnh bay vào giỏ ==== */
.flying-img {
  position: fixed;
  border-radius: 50%;
  z-index: 9999;
  transition: all 0.8s ease;
  pointer-events: none;
}


</style>


{{-- 1) Slider ảnh chính --}}
@php
  $slides = [];
  if ($product->img) $slides[] = asset('storage/'.$product->img);
  $subImgs = is_string($product->sub_img)
           ? json_decode($product->sub_img, true) ?: []
           : (array)$product->sub_img;
  foreach ($subImgs as $p) {
    $slides[] = asset('storage/'.$p);
  }
@endphp
<div class="slider-product mb-3">
  <div class="swiper product-swiper">
    <div class="swiper-wrapper">
      @foreach ($slides as $url)
        <div class="swiper-slide">
          <img src="{{ $url }}" class="img-fluid w-100" alt="">
        </div>
      @endforeach
    </div>
    <div class="swiper-pagination product-swiper-pagination"></div>
  </div>
</div>

<div class="product-show-mobile px-3">
  {{-- 2) Giá --}}
  <div class="mb-3 d-flex align-items-baseline">
    <span class="fs-4 fw-bold me-3 d-flex align-items-center">
      Giá: <strong id="total-price" class="ms-2">{{ number_format($product->base_price,0,',','.') }}₫</strong>
    </span>
    <span class="text-muted d-flex align-items-center" style="text-decoration: line-through;">
      {{ number_format($product->base_price * 1.5,0,',','.') }}₫
    </span>
  </div>

  {{-- 3) Tên --}}
  @php
    $isFav = auth()->check()
      ? auth()->user()->favorites->contains($product->id)
      : in_array($product->id, session('favorites', []));
  @endphp
  <div class="mb-3 d-flex align-items-center justify-content-between">
    <h2 class="mb-0">{{ $product->name }}</h2>
  </div>

  {{-- 3.2) Vận chuyển & đổi trả --}}
  @php
    $start = now()->addDays(5);
    $end   = now()->addDays(6);
  @endphp
  <div class="mb-2 d-flex align-items-center border-top-icon pt-2">
    <i class="fas fa-truck me-2 icon-xanh"></i>
    <span>Miễn phí vận chuyển</span>
    <span class="ms-2">Đảm bảo giao hàng {{ $start->format('d') }}-{{ $end->format('d') }} tháng {{ $start->format('n') }}</span>
  </div>
  <div class="mb-2 d-flex align-items-center border-top-icon pt-2">
    <i class="fas fa-shield-alt me-2 icon-xanh"></i>
    <span>Thanh toán khi giao – Đổi trả miễn phí 14 ngày</span>
  </div>

  {{-- 8) Thumb options --}}
  <div id="option-thumb-bar" class="d-flex align-items-center mb-3 border-top-icon border-bot-icon pt-2 pb-2" style="cursor:pointer;">
    <i class="fa-solid fa-list-ol me-2 icon-xanh"></i>
    <div class="thumb-scroll d-flex flex-grow-1 align-items-center">
      @foreach($optionTypes->first()->values as $val)
        @if($val->option_img)
          <img src="{{ asset('storage/'.$val->option_img) }}"
               alt="{{ $val->value }}"
               class="me-2 rounded"
               style="width:40px;height:40px;object-fit:cover;aspect-ratio:1/1;">
        @endif
      @endforeach
      <span class="text-muted text-nowrap">
        Có {{ $optionTypes->first()->values->count() }} lựa chọn
      </span>
    </div>
    <i class="fa-solid fa-chevron-right ms-2 text-secondary"></i>
  </div>

  {{-- 4) “Có thể bạn cũng thích” --}}
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
    </div>
  </div>

  {{-- 5) Mô tả dài --}}
  @if ($product->long_description)
    <div class="product-long-description mb-4 pt-4">
      {!! $product->long_description !!}
    </div>
  @endif

  {{-- 6) MiMi Cam Kết --}}
  <div class="product-guarantees row text-center mb-4 px-2">
    <h3 class="w-100 mb-3 icon-xanh">MiMi Cam Kết</h3>
    @foreach ([
      ['fa-truck-fast','Giao hàng nhanh chóng'],
      ['fa-arrow-rotate-left','Đổi trả mọi lý do 7 ngày'],
      ['fa-shield','Lỗi 1 đổi 1'],
      ['fa-credit-card','Thanh toán linh hoạt'],
      ['fa-headset','Hỗ trợ 24/7'],
      ['fa-gift','Quà tặng theo chủ đề'],
      ['fa-leaf','Đóng gói chắc chắn'],
      ['fa-plane','Ship nhanh 1-2 ngày'],
      ['fa-truck','Freeship đơn từ 199K'],
      ['fa-tags','Giảm 5% với hội viên'],
      ['fa-bolt','Giao hỏa tốc Hà Nội 2 giờ'],
    ] as $g)
      <div class="col-6 col-md-4 mb-4 d-flex flex-column align-items-center justify-content-start">
        <i class="fas {{ $g[0] }} fs-4 icon-xanh mb-2"></i>
        <span class="small">{{ $g[1] }}</span>
      </div>
    @endforeach
  </div>
</div>

<div id="mobile-overlay"></div>

{{-- Slide-up cart panel --}}
<div id="mobile-cart-panel">
  <div id="mobile-panel-header" class="d-flex align-items-start">
    <div class="d-flex align-items-center">
      <img id="panel-img" src="{{ asset('storage/'.$optionTypes->first()->values->first()->option_img) }}" alt="Chọn thuộc tính">
      <div class="header-info">
        <div id="panel-total-price" class="fw-bold">{{ number_format($product->base_price,0,',','.') }}₫</div>
        <div class="d-flex" style="color:#4ab3af;font-style:italic;">
          <i class="fa-solid fa-truck-fast me-2"></i>
          <p class="mb-0 small">Freeship đơn trên 199.000₫</p>
        </div>
        <div id="panel-selected-names" class="small text-muted"></div>
      </div>
    </div>
    <button id="close-cart-panel"><i class="fa fa-times fa-lg"></i></button>
  </div>

  <form id="add-to-cart-form-mobile" action="{{ route('cart.add', $product->id) }}" method="POST" class="d-flex flex-column h-100">
    @csrf
    <div class="panel-content">
      @foreach ($optionTypes as $type)
        @php $isFirstType = $loop->first; @endphp
        <div class="mb-3">
          <label class="form-label mt-3 mb-0 ms-3">{{ $type->name }}</label>
          <div class="d-flex flex-wrap mb-2 gap-2 ms-2">
            @foreach ($type->values as $val)
              <div class="option-item-show rounded"
                   data-type-id="{{ $type->id }}"
                   data-val-id="{{ $val->id }}"
                   data-extra="{{ $val->extra_price }}"
                   data-img="{{ $val->option_img ? asset('storage/'.$val->option_img) : '' }}">
                @if($isFirstType && $val->option_img)
                  <img src="{{ asset('storage/'.$val->option_img) }}" class="rounded" alt="">
                @endif
                <span>{{ $val->value }}</span>
              </div>
            @endforeach
          </div>
          <input type="hidden" name="options[{{ $type->id }}]" id="option-input-{{ $type->id }}" required>
        </div>
      @endforeach
      <div id="option-error" class="text-danger small" style="display:none;">Vui lòng chọn đầy đủ thuộc tính.</div>
    </div>

    <div class="panel-footer d-flex justify-content-around align-items-center">
      <button type="submit" class="btn-mimi nut-vang">Thêm vào giỏ</button>
      <button type="button" id="buy-now-btn-mobile" class="btn-mimi nut-xanh">Mua ngay</button>
    </div>
  </form>
</div>

{{-- Zoom overlay --}}
<div id="img-zoom-overlay">
  <img id="img-zoom" src="" alt="Zoom">
  <button id="img-zoom-close">&times;</button>
</div>

{{-- Sticky bottom bar --}}
<div id="mobile-cart-bar">
  <button class="btn-favorite border-0 p-0 bg-transparent" data-id="{{ $product->id }}">
    <i class="{{ $isFav ? 'fas text-danger' : 'far text-muted' }} fa-heart"></i>
  </button>
  <div class="d-flex gap-2">
    <button id="open-cart-panel" class="btn-mimi nut-vang">Thêm vào giỏ</button>
    <button id="open-panel-buy" class="btn-mimi nut-xanh">Mua ngay</button>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const panel      = document.getElementById('mobile-cart-panel');
  const overlay    = document.getElementById('mobile-overlay');
  const openBtn    = document.getElementById('open-cart-panel');
  const closeBtn   = document.getElementById('close-cart-panel');
  const form       = document.getElementById('add-to-cart-form-mobile');
  const buyBtn     = document.getElementById('buy-now-btn-mobile');
  const items      = panel.querySelectorAll('.option-item-show');
  const panelImg   = document.getElementById('panel-img');
  const panelPrice = document.getElementById('panel-total-price');
  const panelNames = document.getElementById('panel-selected-names');
  const errorEl    = document.getElementById('option-error');
  const basePrice  = {{ $product->base_price }};
  const firstTypeId= '{{ $optionTypes->first()->id }}';
  const selected   = {};

  function openPanel() {
    panel.classList.add('open');
    overlay.classList.add('open');
    document.body.classList.add('no-scroll');
  }
  function closePanel() {
    panel.classList.remove('open');
    overlay.classList.remove('open');
    document.body.classList.remove('no-scroll');
  }

  openBtn.addEventListener('click', openPanel);
  closeBtn.addEventListener('click', closePanel);
  overlay.addEventListener('click', closePanel);

  document.getElementById('option-thumb-bar')?.addEventListener('click', openPanel);

  function renderPanelHeader() {
    const sumExtra = Object.values(selected).reduce((a,b)=>a+b,0);
    panelPrice.textContent = (basePrice + sumExtra).toLocaleString('vi-VN') + '₫';
    const names = [];
    panel.querySelectorAll('.option-item-show.selected').forEach(el=>{
      names.push(el.textContent.trim());
    });
    panelNames.textContent = names.join(', ');
  }

  items.forEach(el=>{
    el.addEventListener('click', ()=>{
      const typeId = el.dataset.typeId;
      const extra  = parseInt(el.dataset.extra)||0;
      panel.querySelectorAll(`.option-item-show[data-type-id="${typeId}"]`)
           .forEach(x=>x.classList.remove('selected'));
      el.classList.add('selected');
      if(typeId==firstTypeId&&el.dataset.img) panelImg.src=el.dataset.img;
      selected[typeId]=extra;
      document.getElementById(`option-input-${typeId}`).value=el.dataset.valId;
      errorEl.style.display='none';
      renderPanelHeader();
    });
  });

  buyBtn.addEventListener('click', ()=>{
    const missing = Array.from(form.querySelectorAll('input[id^="option-input-"]')).some(i=>!i.value);
    if(missing){ errorEl.style.display='block'; return; }
    form.action="{{ route('checkout.buyNow',$product->id) }}";
    form.submit();
  });

  form.addEventListener('submit', e=>{
    e.preventDefault();
    const missing = Array.from(form.querySelectorAll('input[id^="option-input-"]')).some(i=>!i.value);
    if(missing){ errorEl.style.display='block'; return; }

    fetch(form.action, {
      method:'POST',
      headers:{
        'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,
        'X-Requested-With':'XMLHttpRequest'
      },
      body:new FormData(form)
    })
    .then(r=>r.json())
    .then(json=>{
      if(!json.success) return alert(json.message||'Thêm thất bại');

      // 1) Cập nhật badge
      const cartCountEl = document.getElementById('cart-count-mobile')
                         || document.getElementById('cart-count');
      if(cartCountEl) cartCountEl.textContent=json.total_items;

      // 2) Hiệu ứng bay
      if(panelImg && cartCountEl){
        const fly = panelImg.cloneNode(true);
        fly.classList.add('flying-img');
        const start = panelImg.getBoundingClientRect();
        Object.assign(fly.style,{
          left:start.left+'px', top:start.top+'px',
          width:start.width+'px', height:start.height+'px'
        });
        document.body.appendChild(fly);

        const endRect = cartCountEl.getBoundingClientRect();
        const targetX = endRect.left + endRect.width/2 - start.width/4;
        const targetY = endRect.top  + endRect.height/2- start.height/4;
        requestAnimationFrame(()=>{
          Object.assign(fly.style,{
            left:targetX+'px', top:targetY+'px',
            width:(start.width/2)+'px', height:(start.height/2)+'px',
            opacity:'0.7'
          });
        });
        fly.addEventListener('transitionend',()=>{
          fly.remove();
          closePanel();
        });
        return;
      }

      // 3) Fallback
      closePanel();
    });
  });

  // Favorite toggle
  const csrf = document.querySelector('meta[name="csrf-token"]').content;
  document.querySelectorAll('.btn-favorite').forEach(btn=>{
    btn.addEventListener('click',()=>{
      const id=btn.dataset.id;
      fetch(`/favorites/toggle/${id}`,{
        method:'POST',
        headers:{'X-CSRF-TOKEN':csrf}
      })
      .then(r=>r.json())
      .then(json=>{
        const icon=btn.querySelector('i.fa-heart');
        icon.classList.toggle('fas', json.added);
        icon.classList.toggle('far', !json.added);
        icon.classList.toggle('text-danger', json.added);
        icon.classList.toggle('text-muted', !json.added);
      });
    });
  });

  // Mở panel mua ngay
  document.getElementById('open-panel-buy').addEventListener('click', openPanel);

  // Zoom ảnh
  const zoomOverlay = document.getElementById('img-zoom-overlay');
  const zoomImg     = document.getElementById('img-zoom');
  const zoomClose   = document.getElementById('img-zoom-close');

  function openZoom(){
    zoomImg.src=panelImg.src;
    zoomOverlay.style.display='flex';
    requestAnimationFrame(()=>zoomOverlay.classList.add('open'));
  }
  function closeZoom(){
    zoomOverlay.classList.remove('open');
    setTimeout(()=>zoomOverlay.style.display='none',350);
  }
  panelImg.addEventListener('click',openZoom);
  zoomClose.addEventListener('click',closeZoom);
  zoomOverlay.addEventListener('click',e=>{ if(e.target===zoomOverlay) closeZoom() });
});
</script>
@endpush