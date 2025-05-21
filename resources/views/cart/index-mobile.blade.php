{{-- resources/views/cart/index.blade.php --}}
<link rel="stylesheet" href="{{ Vite::asset('resources/scss/cart.scss') }}">

@extends('layouts.app-mobile')

@section('title', 'Giỏ hàng')

@section('content')
<style>
.trang-gio-hang {
    padding: 1rem 1rem 5rem 1rem;
}
.trang-gio-hang .anh-san-pham {
    width: 150px;
    aspect-ratio: 1 / 1;
    height: auto;
    object-fit: cover;
}
.trang-gio-hang .xoa-border {
    border: 0px solid black !important;
    margin-bottom: 0;
}
.item-qty-input {
  padding: 0;
  text-align: center;
}
    .order-summary {
      position: sticky;
      bottom: 0;
      z-index: 1;
      background: #fff;          
      padding-top: 0.5rem;       
      box-shadow: 0 -2px 8px rgba(0,0,0,0.1);
    }
    .card-body {
    flex: 1 1 auto;
    color: var(--bs-card-color);
    padding: 0.5rem;
}
hr {
  margin: 0.2rem 0 ;
}
/* slide-up panel mặc định ẩn ở dưới */
.checkout-panel {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  height: auto;
  background: #fff;
  transform: translateY(150%);
  transition: transform 0.3s ease;
  z-index: 3;
  box-shadow: 0 -2px 8px rgba(0,0,0,0.2);
  padding: 1rem;
  overflow-y: auto;
}
.checkout-panel.open {
  transform: translateY(0);
}
/* nút Thanh toán dính đáy */
.fixed-checkout-btn {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    padding: 0.8rem;
    background: #4ab3af;
    color: #fff;
    font-size: 1.5rem;
    border: none;
    z-index: 1;
    font-weight: 700;
}
/* nút đóng panel */
.checkout-close {
  position: absolute;
  top: 0.5rem;
  right: 1rem;
  background: transparent;
  border: none;
  font-size: 1.5rem;
  line-height: 1;
}
/* lớp phủ xám, ẩn mặc định */
.checkout-overlay {
  display: none;
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0, 0, 0, 0.3);
  z-index: 2;          
}

/* khi active thì show */
.checkout-overlay.open {
  display: block;
}
.nut-thanh-toan {
background-color: #4ab3af;
    color: white;
    font-size: 1.5rem;
    font-weight: 700;
    padding: 0.5rem;
    border-radius: 100px;
}
.text-free-ship {
            color: #fe3b27;
            font-style: italic;
            font-size: 0.9rem;
        }
.fa-times {
    font-size: 1.5rem;
    -webkit-text-stroke: 2px #b83232;
    color: white;
    padding-top: 1rem;
}

#checkout-toggle {
  position: sticky;
  bottom: 0rem;             /* cách đáy viewport 3rem */
  width: 100%;
  padding: 1rem;
  background: #4ab3af;
  color: #fff;
  font-size: 1.25rem;
  border: none;
  z-index: 1;
}


</style>
<div class="cart-wrapper">
  
<div class="container trang-gio-hang">
  @if (count($cart))
    <div class="row">
      {{-- ===== Cột trái: Bag ===== --}}
      <div class="col-md-8">
        <h3 class="mb-4">Sản Phẩm</h3>

        @foreach($cart as $key => $item)
          <div class="d-flex align-items-start mb-4">
            {{-- Checkbox chọn sản phẩm --}}
            <div class="me-3">
              <input 
                type="checkbox"
                name="selected_ids[]"
                value="{{ $key }}"
                form="checkout-form"
                class="rowCheck"
                data-price="{{ $item['price'] }}"
                data-qty="{{ $item['quantity'] }}">
            </div>

            {{-- Ảnh + nút tăng/giảm --}}
            <div class="text-center">
            <a href="{{ route('products.show', $item['slug']) }}">
              <img src="{{ asset('storage/'.$item['image']) }}"
                  class="img-fluid rounded anh-san-pham"
                  alt="{{ $item['name'] }}">
            </a>

              <div class="d-flex tang-giam-va-tim">
                <div class="tang-giam-cont ">
                  <form action="{{ route('cart.update', $key) }}"
                        method="POST"
                        class="d-flex xoa-border">
                    @csrf
                    <button
                      type="button"
                      class="btn btn-outline-secondary btn-sm xoa-border qty-btn minus"
                      data-key="{{ $key }}"
                      data-action="dec"
                    >
                      @if($item['quantity'] > 1)
                        &minus;
                      @else
                        <i class="fa fa-trash"></i>
                      @endif
                    </button>

                    <input
                      type="number"
                      name="quantity"
                      value="{{ $item['quantity'] }}"
                      min="1"
                      class="item-qty-input btn btn-outline-secondary btn-sm mx-1 xoa-border text-center"
                      style="width: 100%;"
                    />


                    <button
                      type="button"
                      class="btn btn-outline-secondary btn-sm xoa-border qty-btn plus"
                      data-key="{{ $key }}"
                      data-action="inc"
                    >
                      +
                    </button>

                  </form>
                </div>

                @php
                  // Kiểm tra xem đã favorite chưa: auth → DB, guest → session
                  $isFav = auth()->check()
                    ? auth()->user()->favorites->contains($item['product_id'])
                    : in_array($item['product_id'], session('favorites', []));
                @endphp

                

              </div>            
            </div>

            {{-- Thông tin sản phẩm --}}
            <div class="ms-4 flex-grow-1">
              <div class="d-flex flex-column justify-content-between">
              <h5 class="mb-1">
              <a href="{{ route('products.show', $item['slug']) }}"
                class="text-decoration-none ten-san-pham-an-duoc w-100">
                {{ $item['name'] }}
              </a>
              
              </h5>
                <span class="fw-bold mt-2">
                  {{ number_format($item['price'],0,',','.') }}₫
                </span>
              </div>

              @php
                $vals = \App\Models\OptionValue::whereIn('id', $item['options'] ?? [])
                          ->with('type')->get();
              @endphp
              @foreach($vals as $val)
                <div class="text-muted">
                  <strong>{{ $val->type->name }}:</strong>
                  {{ $val->value }}
                </div>
              @endforeach
            </div>         
          </div>
         
        @endforeach
      </div>

    </div>
  @else
    <p class="text-center">Giỏ hàng trống!</p>
  @endif
</div>
  {{-- Lớp phủ xám --}}
  <div id="checkout-overlay" class="checkout-overlay"></div>
  {{-- Slide-up panel chứa toàn bộ summary --}}
  <div id="checkout-panel" class="checkout-panel">
    <button id="checkout-close" class="checkout-close"><i class="fa fa-times fa-lg"></i></button>
    <h3 class="mb-3">Thanh Toán</h3>
    <form id="checkout-form" action="{{ route('checkout.show') }}" method="GET">
      @csrf
      <div class="card">
        <div class="card-body">
          <p class="d-flex justify-content-between mb-3">
            <span>Thành Tiền</span>
            <span id="summary-subtotal">0₫</span>
          </p>
          <p class="d-flex justify-content-between mb-3">
            <span>Phí Ship</span>
            <span id="summary-shipping">0₫</span>
          </p>
          <p id="freeship-msg" class="text-free-ship mb-2">
            (FreeShip với đơn hàng &gt; 199.000₫)
          </p>
          <hr>
          <p class="d-flex justify-content-between mb-3 fw-bold mt-3">
            <span>Tổng Cộng</span>
            <span id="summary-grandtotal">0₫</span>
          </p>
          <p id="checkout-warning" class="text-center" style="display:none; color:#b83232; margin-bottom:8px;">
            Bạn vui lòng chọn sản phẩm trước khi thanh toán nha
          </p>
          {{-- Nút Xác nhận thanh toán mới --}}
          <button
            type="button"
            id="checkout-submit"
            class="w-100 nut-thanh-toan mt-3 border-0"
          >
            Xác nhận thanh toán
          </button>
        </div>
      </div>
    </form>
      {{-- Nút dính đáy, chỉ hiện “Thanh toán” --}}

  </div>


  <button id="checkout-toggle" class="fixed-checkout-btn">
    Thanh toán
  </button>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // 1) Summary logic
  const boxes      = document.querySelectorAll('input.rowCheck');
  const subtotalEl = document.getElementById('summary-subtotal');
  const shipEl     = document.getElementById('summary-shipping');
  const grandEl    = document.getElementById('summary-grandtotal');
  const warnEl     = document.getElementById('checkout-warning');

  function recalcSummary() {
    let subtotal = 0;
    boxes.forEach(cb => {
      if (cb.checked) {
        subtotal += (parseInt(cb.dataset.price)||0) * (parseInt(cb.dataset.qty)||1);
      }
    });
    const shipping = subtotal > 199000 ? 0 : (subtotal===0 ? 0 : 20000);
    const grand    = subtotal + shipping;

    subtotalEl.textContent = subtotal.toLocaleString('vi-VN') + '₫';
    shipEl.textContent     = (shipping===0 && subtotal>0)
                             ? 'Free'
                             : shipping.toLocaleString('vi-VN') + '₫';
    grandEl.textContent    = grand.toLocaleString('vi-VN') + '₫';
  }

  boxes.forEach(cb => cb.addEventListener('change', () => {
    recalcSummary();
    warnEl.style.display = 'none';
  }));
  recalcSummary();

  // 2) AJAX update quantity
  const token     = document.querySelector('meta[name="csrf-token"]').content;
  const updateUrl = "{{ url('/cart/update') }}";

  async function updateCart({ rowKey, action=null, quantity=null }) {
    try {
      const payload = action ? { action } : { quantity };
      const res = await fetch(`${updateUrl}/${rowKey}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN':     token,
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type':     'application/json',
        },
        body: JSON.stringify(payload)
      });
      if (!res.ok) throw new Error();
      const data = await res.json();
      if (data.success) {
        const btnDec  = document.querySelector(`.qty-btn[data-key="${rowKey}"].minus`);
        const row     = btnDec.closest('.d-flex.align-items-start');
        const inputEl = row.querySelector('.item-qty-input');
        inputEl.value = data.quantity;
        const cb = row.querySelector('input.rowCheck');
        if (cb) cb.dataset.qty = data.quantity;
        btnDec.innerHTML = data.quantity>1
          ? '&minus;'
          : '<i class="fa fa-trash"></i>';
        if (data.quantity===0) row.remove();
        recalcSummary();
      }
    } catch(err){
      console.error('Lỗi cập nhật giỏ hàng:', err);
    }
  }

  document.querySelectorAll('.qty-btn').forEach(btn =>
    btn.addEventListener('click', () => {
      updateCart({ rowKey: btn.dataset.key, action: btn.dataset.action });
    })
  );
  document.querySelectorAll('.item-qty-input').forEach(input => {
    input.addEventListener('blur', () => {
      const n = parseInt(input.value);
      updateCart({
        rowKey: input.closest('form').querySelector('.qty-btn').dataset.key,
        quantity: isNaN(n)||n<1 ? 1 : n
      });
    });
    input.addEventListener('keydown', e => {
      if (e.key==='Enter') { e.preventDefault(); input.blur(); }
    });
  });

  // 3) Toggle favorite
  document.querySelectorAll('.btn-favorite').forEach(btn =>
    btn.addEventListener('click', () => {
      fetch(`/favorites/toggle/${btn.dataset.id}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': token }
      })
      .then(r=>r.json())
      .then(json => {
        const icon = btn.querySelector('i.fa-heart');
        icon.classList.toggle('far', !json.added);
        icon.classList.toggle('fas', json.added);
      })
      .catch(console.error);
    })
  );

  // 4) Panel toggle + overlay
  const panelToggle = document.getElementById('checkout-toggle');
  const panel       = document.getElementById('checkout-panel');
  const panelClose  = document.getElementById('checkout-close');
  const overlay     = document.getElementById('checkout-overlay');

  function openPanel(){
    recalcSummary();
    warnEl.style.display = 'none';
    panel.classList.add('open');
    overlay.classList.add('open');
  }
  function closePanel(){
    panel.classList.remove('open');
    overlay.classList.remove('open');
  }

  panelToggle.addEventListener('click', openPanel);
  panelClose.addEventListener('click', closePanel);
  overlay.addEventListener('click', closePanel);

  // 5) Xác nhận thanh toán
  document.getElementById('checkout-submit').addEventListener('click', () => {
    const selected = Array.from(boxes)
      .filter(cb => cb.checked)
      .map(cb => cb.value);
    if (!selected.length) {
      warnEl.style.display = 'block';
      return;
    }
    const url = new URL("{{ route('checkout.show') }}", window.location.origin);
    selected.forEach(id => url.searchParams.append('selected_ids[]', id));
    window.location.href = url;
  });


});
</script>
@endpush








