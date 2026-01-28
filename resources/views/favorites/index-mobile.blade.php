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
  z-index: 10001;
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
  justify-content: space-around;
  align-items: center;
}
.bg-primary {
    --bs-bg-opacity: 1;
    background-color: #4ab3af !important;
}
.fa-times {
    font-size: 1.5rem;
    -webkit-text-stroke: 2px #b83232;
    color: white;
}
  .flying-img {
    position: fixed;
    border-radius: 50%;
    z-index: 9999;
    transition: all 0.8s ease;
    pointer-events: none;
  }
  .nut-xanh {
    width: 100%;
  }
</style>
<div class="trang-yeu-thich-mobile ms-1 me-1 pt-3 pb-3">
    <h1 class="pb-3 ps-1 text-center">Sản phẩm yêu thích</h1>
    @if($products->isEmpty())
      <div class="p-3 bg-white rounded-3 ms-1 me-1">
        <p class="m-0" style="font-weight:600;">Chưa có sản phẩm yêu thích nào.</p>
        <p class="mb-0 text-muted">Bấm vào biểu tượng ❤️ ở trang sản phẩm để lưu lại nha.</p>
        <a class="btn-mimi nut-xanh mt-3 d-inline-block" href="{{ route('products.index') }}">Đi xem sản phẩm</a>
      </div>
    @endif
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
    <div id="detail-{{ $p->id }}" class="detail-panel" data-base-price="{{ $p->base_price }}">
    {{-- Header giống #mobile-panel-header --}}
    <div class="panel-header">
        <div class="d-flex align-items-start">
        @if($p->img)
            <img src="{{ asset('storage/'.$p->img) }}" alt="{{ $p->name }}">
        @endif
        <div class="header-info">
            <h5 style="max-width: 88%;" class="m-0 xuong-2-dong">{{ $p->name }}</h5>
            <div class="d-flex" style="color:#4ab3af;font-style:italic;">
                <i class="fa-solid fa-truck-fast me-2"></i>
                <p class="mb-0 small">Freeship đơn trên 199.000₫</p>
            </div>
            <div class="fw-bold">{{ number_format($p->base_price,0,',','.') }}₫</div>
        </div>
        </div>
        <button class="panel-close" data-id="{{ $p->id }}"><i class="fa fa-times fa-lg"></i></button>
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
                        style="padding:0.2rem .3rem; border:1px solid #4ab3af; border-radius: 5px; margin:.2rem; cursor:pointer; display:flex; align-items:center;">
                    
                    @if($isFirst && $val->option_img)
                        <img src="{{ asset('storage/'.$val->option_img) }}"
                            alt="{{ $val->value }}"
                            style="width:40px; height:40px; object-fit:cover; margin-right:.3rem; border-radius: 5px;">
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
         <button
             class="btn-mimi nut-xanh btn-add-fav-cart mt-2"
             data-id="{{ $p->id }}"
             data-url="{{ route('cart.add', $p->id) }}"
           >
             Thêm vào giỏ
           </button>
        <button 
            type="button"
            class="btn-mimi nut-do btn-buy-now mt-2"
            data-id="{{ $p->id }}"
            data-buy-url="{{ route('checkout.buyNow', $p->id) }}"
        >
        Mua ngay
        </button>
    </div>
    </div>

    @endforeach

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const csrf    = document.querySelector('meta[name="csrf-token"]').content;
  const overlay = document.getElementById('detail-overlay');
  const panels  = document.querySelectorAll('.detail-panel');
  const openBtns  = document.querySelectorAll('.btn-detail');
  const closeBtns = document.querySelectorAll('.panel-close');

  // 1) Hàm mở/đóng panel chi tiết
  function openDetail(id) {
    const panel = document.getElementById('detail-' + id);
    panel.classList.add('open');
    overlay.classList.add('open');
    document.body.classList.add('no-scroll');
  }
  function closeAllDetails() {
    panels.forEach(p => p.classList.remove('open'));
    overlay.classList.remove('open');
    document.body.classList.remove('no-scroll');
  }
  openBtns.forEach(b => b.addEventListener('click', () => openDetail(b.dataset.id)));
  closeBtns.forEach(b => b.addEventListener('click', closeAllDetails));
  overlay.addEventListener('click', closeAllDetails);

  // 2) Với mỗi detail-panel, khởi tạo chọn tuỳ chọn, AJAX form và nút “Thêm vào giỏ”
  panels.forEach(panel => {
    const pid         = panel.id.replace('detail-','');
    const form        = panel.querySelector('form');
    const opts        = panel.querySelectorAll('.option-item');
    const headerImg   = panel.querySelector('.panel-header img');
    const headerPrice = panel.querySelector('.panel-header .header-info .fw-bold');
    // Lấy base price theo từng panel (tránh lỗi khi danh sách favorites rỗng)
    const basePrice   = parseInt(panel.dataset.basePrice || '0', 10);
    const selectedExtras = {};

    // 2.1) Chọn tuỳ chọn
    opts.forEach(el => {
      el.addEventListener('click', () => {
        const type  = el.dataset.type;
        const val   = el.dataset.val;
        const extra = parseInt(el.dataset.extra) || 0;

        // highlight
        panel.querySelectorAll(`.option-item[data-type="${type}"]`)
             .forEach(x => x.classList.remove('bg-primary','text-white'));
        el.classList.add('bg-primary','text-white');

        // cập nhật hidden input
        panel.querySelector(`#opt-${pid}-${type}`).value = val;

        // đổi ảnh nếu là nhóm đầu
        if (el.closest('.option-group').dataset.first === '1' && el.dataset.img) {
          headerImg.src = el.dataset.img;
        }

        // cập nhật giá
        selectedExtras[type] = extra;
        const sumExtra = Object.values(selectedExtras).reduce((a,b)=>a+b,0);
        headerPrice.textContent = (basePrice + sumExtra).toLocaleString('vi-VN') + '₫';
      });
    });

    // 2.2) Hàm AJAX chung
    function ajaxAdd() {
      // validate
      const missing = Array.from(panel.querySelectorAll('input[type="hidden"]'))
                           .some(i => !i.value);
      if (missing) {
        return alert('Vui lòng chọn đủ tuỳ chọn trước khi thêm vào giỏ!');
      }

      fetch(form.action, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrf,
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: new FormData(form)
      })
      .then(r => r.json())
      .then(json => {
        if (!json.success) {
          return alert(json.message || 'Thêm thất bại 😢');
        }

        // cập nhật badge
        const cartCountEl = document.getElementById('cart-count-mobile')
                           || document.getElementById('cart-count');
        if (cartCountEl) {
          cartCountEl.textContent = json.total_items;
          if (json.total_items > 0) cartCountEl.style.display = 'inline-block';
        }
        // cập nhật menu mobile nếu có
        const cartMenu = document.getElementById('cartMenuMobile');
        if (cartMenu) {
          fetch("{{ route('cart.menu-mobile') }}")
            .then(r => r.text())
            .then(html => cartMenu.innerHTML = html);
        }

        // hiệu ứng ảnh bay
        const fly = headerImg.cloneNode(true);
        fly.classList.add('flying-img');
        const start = headerImg.getBoundingClientRect();
        Object.assign(fly.style, {
          left:  start.left  + 'px',
          top:   start.top   + 'px',
          width: start.width + 'px',
          height:start.height+ 'px'
        });
        document.body.appendChild(fly);

        const targetEl = (cartCountEl && cartCountEl.offsetWidth>0)
                          ? cartCountEl
                          : document.getElementById('cartDropdownMobile');
        const end = targetEl.getBoundingClientRect();
        requestAnimationFrame(() => {
          Object.assign(fly.style, {
            left:   (end.left + end.width/2 - start.width/4) + 'px',
            top:    (end.top  + end.height/2 - start.height/4) + 'px',
            width:  (start.width/2) + 'px',
            height: (start.height/2)+ 'px',
            opacity:'0.7'
          });
        });
        fly.addEventListener('transitionend', () => fly.remove());

        // đóng panel
        closeAllDetails();
      })
      .catch(() => alert('Không thể kết nối máy chủ 😵'));
    }

    // 2.3) Bắt sự kiện form submit (nếu vẫn còn)
    form.addEventListener('submit', e => {
      e.preventDefault();
      ajaxAdd();
    });

    // 2.4) Bắt sự kiện nút “Thêm vào giỏ” mới
    const addBtn = panel.querySelector('.btn-add-fav-cart');
    if (addBtn) {
      addBtn.addEventListener('click', ajaxAdd);
    }
  });

  // 3) Toggle yêu thích
  document.querySelectorAll('.btn-favorite').forEach(btn => {
    btn.addEventListener('click', () => {
      fetch(`{{ url('/favorites/toggle') }}/${btn.dataset.id}`, {
        method:'POST',
        credentials: 'same-origin',
        headers:{ 'X-CSRF-TOKEN': csrf }
      })
      .then(r => r.json())
      .then(json => {
        const ic = btn.querySelector('i.fa-heart');
        ic.classList.toggle('fas', json.added);
        ic.classList.toggle('far', !json.added);
        ic.classList.toggle('text-danger', json.added);
        ic.classList.toggle('text-muted', !json.added);
      })
      .catch(() => alert('Không thể cập nhật yêu thích 😵'));
    });
  });
    // 4) Mua ngay – đẩy thẳng lên checkout
  document.querySelectorAll('.btn-buy-now').forEach(btn => {
    btn.addEventListener('click', () => {
      const pid     = btn.dataset.id;
      const panel   = document.getElementById('detail-' + pid);
      const inputs  = Array.from(panel.querySelectorAll('input[type="hidden"][id^="opt-"]'));
      // 4.1) Validate đã chọn đủ
      const missing = inputs.some(i => !i.value);
      if (missing) {
        return alert('Vui lòng chọn đầy đủ các phân loại');
      }
      // 4.2) Tính tổng giá cơ bản + extras
      let base    = parseInt(panel.dataset.basePrice || '0', 10);
      let sumExtra = inputs.reduce((s,i) => s + parseInt(i.value) * 0 + /* mình lấy extra*/ 0, 0);
      // THỰC TẾ: ta đã lưu extra ở controller, frontend không cần tính lại để checkout xử lý
      // 4.3) Tạo form động gửi POST tới buyNow
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = btn.dataset.buyUrl;
      form.style.display = 'none';
      // CSRF
      const token = document.querySelector('meta[name="csrf-token"]').content;
      const inpCsrf = document.createElement('input');
      inpCsrf.name  = '_token';
      inpCsrf.value = token;
      form.appendChild(inpCsrf);
      // Options
      inputs.forEach(i => {
        // id format: opt-<pid>-<typeId>
        const typeId = i.id.split('-').pop();
        const inp    = document.createElement('input');
        inp.type     = 'hidden';
        inp.name     = `options[${typeId}]`;
        inp.value    = i.value;
        form.appendChild(inp);
      });
      document.body.appendChild(form);
      form.submit();
    });
  });

});
</script>
@endpush





