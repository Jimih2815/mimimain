@extends('layouts.app-mobile')
@section('title','Yêu thích (Mobile)')

@section('content')
<style>
    .trang-yeu-thich-mobile {

    }
  .cart-wrapper { padding-bottom: calc(4rem + 3rem); }
  .row-products {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 10px;
}
.product-card {
    width: 48.5%;
    background: #fff;
    border-radius: 0.5rem;
    overflow: hidden;
    margin-bottom: 1.5rem;
}
  .product-card img {
    width: 100%;
    display: block;
    aspect-ratio: 1 / 1;
    object-fit: cover;
    overflow: hidden;
}
  .product-body { 
      display: flex;
  flex-direction: column;
  justify-content: space-between;
    padding: 0.5rem; 
    height: 7.5rem;
}
  .product-body .price {
    color: #fe3b27;
    font-weight: 600;
    font-size: 1.2rem;
}
  .btn-detail { width:100%; margin-top:0.5rem; }
  /* panel chung */
  .detail-panel {
    position: fixed; bottom:0; left:0;
    width:100%; height:auto;
    background:#fff;
    transform: translateY(100%);
    transition: transform .3s ease;
    z-index: 1002;
    overflow-y: auto;
  }
  .detail-panel.open { transform: translateY(0); }
  .detail-overlay {
    display:none; position:fixed;
    top:0;left:0;width:100%;height:100%;
    background:rgba(0,0,0,0.3); z-index:5;
  }
  .detail-overlay.open { display:block; }
  .panel-close { position:absolute; top:1rem; right:1rem; font-size:1.5rem; border:none; background:transparent; }
  .panel-content { padding:2rem 1rem 1rem; 
height: 100%;}
  .cart-button { position: sticky; bottom:3rem; width:100%; padding:1rem; background:#4ab3af; color:#fff; border:none; font-weight:700; }
   .nut-xem {
    color: #4ab3af;
    font-size: 1rem;
    font-weight: 700;
    padding: 0;
    height: 2rem;
  }
  .fa-arrow-right {
    font-size: 1rem;
  }
    .original-price {
    /* tuỳ chỉnh font-size nếu cần */
    /* ví dụ nhỏ hơn một chút cho đỡ chiếm chỗ */
    font-size: 0.85rem;
    text-decoration: line-through;
    }
/* Khi panel mở, khóa scroll cho body */
body.no-scroll { overflow: hidden; }

/* Container panel */
.detail-panel {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  max-height: 80vh;                   /* giống mobile-cart-panel */
  background: #fff;
  box-shadow: 0 -2px 8px rgba(0,0,0,0.1);
  transform: translateY(150%);
  transition: transform .3s ease;
  z-index: 100;
  display: flex;
  flex-direction: column;
}
.detail-panel.open {
  transform: translateY(0);
}

/* Header – dính trên */
.detail-panel .panel-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem;
  border-bottom: 1px solid #eee;
  flex: 0 0 auto;
}
.detail-panel .panel-header img {
  width: 80px;
  aspect-ratio: 1/1;
  object-fit: cover;
  margin-right: .75rem;
}
.detail-panel .panel-header .header-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: .25rem;
}

/* Content – cuộn khi dài */
.detail-panel .panel-content {
  flex: 1 1 auto;
  min-height: 0;     /* bắt buộc để flex scroll ngon trên Safari/Chrome mobile */
  overflow-y: auto;
  padding: 1rem;
}

/* Footer – dính dưới */
.detail-panel .panel-footer {
  flex: 0 0 auto;
  padding: .75rem;
  border-top: 1px solid #eee;
  background: #fff;
  display: flex;
  gap: .5rem;
}

</style>
<div class="trang-yeu-thich-mobile ms-1 me-1 pt-3 pb-3">
    <h1 class="pb-3 ps-1">Sản phẩm yêu thích</h1>
    <div class="cart-wrapper">
    <div class="row-products">
        @foreach($products as $p)
            <div class="product-card" data-id="{{ $p->id }}">
                @if($p->img)
                <a href="{{ route('products.show', $p->slug) }}" class="d-block">
                    <img src="{{ asset('storage/'.$p->img) }}" alt="{{ $p->name }}">
                </a>
                @endif
                <div class="product-body">
                <h6 class="xuong-2-dong m-0">{{ $p->name }}</h6>
                <div class="d-flex align-items-center mt-2">
                    <!-- Giá sale (giá gốc bạn muốn hiển thị) -->
                    <div class="price">
                    {{ number_format($p->base_price, 0, ',', '.') }}₫
                    </div>
                    <!-- Giá “đắt xắt ra miếng” cộng thêm 40%, gạch ngang -->
                    <div class="original-price ms-2 text-muted" style="text-decoration: line-through;">
                    {{ number_format($p->base_price * 1.4, 0, ',', '.') }}₫
                    </div>
                </div>
                <button class="nut-xem border-0 bg-transparent btn-detail d-flex justify-content-center align-items-center mt-auto" data-id="{{ $p->id }}">
                    <p class="m-0 p-0 d-flex justify-content-center align-items-center">Chi Tiết</p>
                    <i class="ms-2 fa-solid fa-arrow-right d-flex justify-content-center align-items-center"></i>
                </button>
                </div>
            </div>
        @endforeach

    </div>
    </div>

    <div id="detail-overlay" class="detail-overlay"></div>

    @foreach($products as $p)
    {{-- Panel riêng cho mỗi sản phẩm --}}
    <div id="detail-{{ $p->id }}" class="detail-panel">
    {{-- Header giống #mobile-panel-header --}}
    <div class="panel-header">
        <div class="d-flex align-items-center">
        @if($p->img)
            <img src="{{ asset('storage/'.$p->img) }}" alt="{{ $p->name }}">
        @endif
        <div class="header-info">
            <h5 class="m-0">{{ $p->name }}</h5>
            <div class="fw-bold">{{ number_format($p->base_price,0,',','.') }}₫</div>
        </div>
        </div>
        <button class="panel-close" data-id="{{ $p->id }}">&times;</button>
    </div>

    {{-- Content cuộn được --}}
    <div class="panel-content">
        <form id="detail-form-{{ $p->id }}"
            action="{{ route('cart.add', $p->id) }}"
            method="POST"
            class="d-flex flex-column h-100">
        @csrf

        {{-- Options như cũ --}}
        @foreach($p->optionValues->groupBy(fn($v)=>$v->type->name) as $typeName => $vals)
            @php 
                $typeId  = $vals->first()->type->id; 
                $isFirst = $loop->first;
            @endphp
            <div class="mb-2">
                <label class="form-label">{{ $typeName }}</label>
                <div class="d-flex flex-wrap option-group" data-first="{{ $isFirst?1:0 }}">
                @foreach($vals as $val)
                    <div class="option-item"
                        data-type="{{ $typeId }}"
                        data-val="{{ $val->id }}"
                        data-extra="{{ $val->extra_price }}"
                        data-img="{{ $val->option_img ? asset('storage/'.$val->option_img) : '' }}"
                        style="padding:.3rem .6rem; border:1px solid #ccc; border-radius:.25rem; margin:.2rem; cursor:pointer; display:flex; align-items:center;">
                    
                    @if($isFirst && $val->option_img)
                        <img src="{{ asset('storage/'.$val->option_img) }}"
                            alt="{{ $val->value }}"
                            style="width:40px; height:40px; object-fit:cover; margin-right:.3rem;">
                    @endif
                    
                    <span>{{ $val->value }}</span>
                    </div>
                @endforeach
                </div>
                <input type="hidden"
                    name="options[{{ $typeId }}]"
                    id="opt-{{ $p->id }}-{{ $typeId }}"
                    required>
            </div>
        @endforeach


        </form>
    </div>

    {{-- Footer với nút Thêm/Mua --}}
    <div class="panel-footer">
        <button type="button"
                class="btn btn-primary flex-fill"
                onclick="document.getElementById('detail-form-{{ $p->id }}').submit()">
        Thêm vào giỏ
        </button>
        <button type="button"
                class="btn btn-success flex-fill"
                onclick="/* nếu có buyNow tương tự */">
        Mua ngay
        </button>
    </div>
    </div>

    @endforeach

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const overlay = document.getElementById('detail-overlay');

  // Mở panel
  function openPanel(id) {
    const panel = document.getElementById('detail-' + id);
    panel.classList.add('open');
    overlay.classList.add('open');
    document.body.classList.add('no-scroll');
  }
  // Đóng panel
  function closePanel(id) {
    const panel = document.getElementById('detail-' + id);
    panel.classList.remove('open');
    overlay.classList.remove('open');
    document.body.classList.remove('no-scroll');
  }

  // Gán sự kiện cho nút Chi Tiết
  document.querySelectorAll('.btn-detail').forEach(btn => {
    btn.addEventListener('click', () => openPanel(btn.dataset.id));
  });
  // Gán cho nút đóng
  document.querySelectorAll('.panel-close').forEach(btn => {
    btn.addEventListener('click', () => closePanel(btn.dataset.id));
  });
  // Click overlay đóng hết
  overlay.addEventListener('click', () => {
    document.querySelectorAll('.detail-panel.open')
            .forEach(panel => panel.classList.remove('open'));
    overlay.classList.remove('open');
    document.body.classList.remove('no-scroll');
  });

  // Logic chọn option, cập nhật ảnh và giá
  @foreach($products as $p)
  (function(){
    const pid         = '{{ $p->id }}';
    const panel       = document.getElementById('detail-' + pid);
    const form        = document.getElementById('detail-form-' + pid);
    const opts        = panel.querySelectorAll('.option-item');
    const headerImg   = panel.querySelector('.panel-header img');
    const headerPrice = panel.querySelector('.panel-header .header-info .fw-bold');
    const basePrice   = {{ $p->base_price }};
    const selectedExtras = {}; // lưu extra_price từng loại

    opts.forEach(el => {
      el.addEventListener('click', () => {
        const type  = el.dataset.type;
        const val   = el.dataset.val;
        const extra = parseInt(el.dataset.extra) || 0;

        // 1) Highlight lựa chọn
        panel.querySelectorAll(`.option-item[data-type="${type}"]`)
             .forEach(x => x.classList.remove('bg-primary','text-white'));
        el.classList.add('bg-primary','text-white');

        // 2) Cập nhật hidden input
        panel.querySelector(`#opt-${pid}-${type}`).value = val;

        // 3) Nếu là nhóm đầu tiên, đổi ảnh header
        const groupEl = el.closest('.option-group');
        if (groupEl.dataset.first === '1' && el.dataset.img && headerImg) {
          headerImg.src = el.dataset.img;
        }

        // 4) Cập nhật extra_price đã chọn
        selectedExtras[type] = extra;

        // 5) Tính tổng và cập nhật giá trong header
        const sumExtra = Object.values(selectedExtras).reduce((a, b) => a + b, 0);
        headerPrice.textContent = (basePrice + sumExtra).toLocaleString('vi-VN') + '₫';
      });
    });

    // Validate trước khi submit
    form.addEventListener('submit', e => {
      const missing = Array.from(panel.querySelectorAll('input[type="hidden"]'))
                           .some(i => !i.value);
      if (missing) {
        e.preventDefault();
        alert('Vui lòng chọn đủ tuỳ chọn trước khi thêm vào giỏ!');
      }
    });
  })();
  @endforeach

});
</script>
@endpush



