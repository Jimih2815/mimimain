@extends('layouts.app')

@section('content')
<div class="py-4 tat-ca-san-pham-cont favorite-cont">
  <h1 class="mb-4">Danh sách Sản phẩm yêu thích</h1>

  <div class="row align-items-start san-pham-cont">
    @forelse($products as $product)
      <div class="col-6 col-md-4 col-lg-3 list-san-pham favorite-card" data-id="{{ $product->id }}">
        <div class="card h-100 product-card" data-product-id="{{ $product->id }}">
          @if($product->img)
            <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none">
              <img
                src="{{ asset('storage/'.$product->img) }}"
                class="card-img-top product-main-img"
                alt="{{ $product->name }}"
                id="main-img-{{ $product->id }}">
            </a>
          @endif

          <div class="card-body-favorite d-flex flex-column noi-chua-nut-favorites-2">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h5 class="card-title mb-0">
                <a href="{{ route('products.show', $product->slug) }}"
                   class="text-decoration-none text-dark">
                  {{ $product->name }}
                </a>
              </h5>
              <button type="button"
                      class="btn btn-sm btn-fav-toggle p-0 btn-favorite-2"
                      data-id="{{ $product->id }}">
                <i class="fas fa-heart text-danger"></i>
              </button>
            </div>

            <p class="card-text text-muted mb-2">
              Giá: <strong id="total-price-{{ $product->id }}">{{ number_format($product->base_price,0,',','.') }}₫</strong>
            </p>

            <p class="card-text xem-chi-tiet">
              <i class="fas fa-long-arrow-alt-right"></i> Xem Chi Tiết
            </p>

            <form action="{{ route('cart.add', $product->id) }}"
                  method="POST"
                  id="add-to-cart-form-{{ $product->id }}"
                  class="mt-auto">
              @csrf

              {{-- 1) Tuỳ chọn --}}
              @foreach($product->optionValues->groupBy(fn($v)=>$v->type->name) as $typeName => $values)
                @php $typeId = $values->first()->type->id; @endphp
                <div class="mb-3">
                  <label class="form-label">{{ $typeName }}</label>
                  <div class="d-flex flex-row option-items-show mb-2"
                      data-first-group="{{ $loop->first ? '1':'0' }}">
                    @foreach($values as $val)
                      <div class="option-item-show"
                          data-type-id="{{ $typeId }}"
                          data-val-id="{{ $val->id }}"
                          data-extra="{{ $val->extra_price }}"
                          data-img="{{ $val->option_img ? asset('storage/'.$val->option_img) : '' }}">
                        {{ $val->value }}
                      </div>
                    @endforeach
                  </div>
                  <input type="hidden"
                        name="options[{{ $typeId }}]"
                        id="option-input-{{ $product->id }}-{{ $typeId }}"
                        required>
                </div>
              @endforeach

              {{-- 2) Thông báo lỗi --}}
              <div id="option-error-{{ $product->id }}"
                  class="text-danger small mb-2"
                  style="display:none;">
                !!! Vui lòng chọn các tuỳ chọn.
              </div>

              {{-- 3) Hidden inputs final price & image --}}
              <input type="hidden"
                    name="final_price"
                    id="final-price-{{ $product->id }}"
                    value="{{ $product->base_price }}">
              <input type="hidden"
                    name="selected_img"
                    id="selected-img-{{ $product->id }}"
                    value="">

              {{-- 4) Nút thêm giỏ hàng --}}
              <button type="submit"
                      class="btn btn-them-gio w-100">
                Thêm vào giỏ hàng
              </button>
            </form>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12 text-center py-5">
        <p>Chưa có sản phẩm nào trong danh sách yêu thích.</p>
      </div>
    @endforelse
  </div>
</div>

@include('partials.back-to-top-full')
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const csrf = document.querySelector('meta[name="csrf-token"]').content;

  // 1) Toggle yêu thích
  document.querySelectorAll('.btn-fav-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      const pid = btn.dataset.id;
      fetch(`/favorites/toggle/${pid}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        }
      })
      .then(res => res.json())
      .then(json => {
        const icon = btn.querySelector('i.fa-heart');
        const card = btn.closest('.favorite-card');
        if (json.added) icon.classList.replace('far','fas');
        else {
          icon.classList.replace('fas','far');
          if (card) card.remove();
        }
      })
      .catch(() => alert('Có lỗi, vui lòng thử lại.'));
    });
  });

  // 2) AJAX “Thêm vào giỏ hàng” + validation + badge + header-list + notification
  document.querySelectorAll('form[id^="add-to-cart-form-"]').forEach(form => {
    if (form.dataset.bound) return;
    form.dataset.bound = '1';

    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const pid     = this.id.replace('add-to-cart-form-','');
      const errorEl = document.getElementById(`option-error-${pid}`);
      const inputs  = this.querySelectorAll(`input[id^="option-input-${pid}-"]`);
      const missing = Array.from(inputs).some(i => !i.value);

      if (missing) {
        if (errorEl) errorEl.style.display = 'block';
        return;
      }
      if (errorEl) errorEl.style.display = 'none';

      const btn      = this.querySelector('button[type="submit"]');
      const origText = btn.textContent;
      const data     = new FormData(this);

      fetch(this.action, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN'     : csrf,
          'X-Requested-With' : 'XMLHttpRequest'
        },
        body: data
      })
      .then(res => res.json())
      .then(json => {
        if (json.success) {
          // a) Cập nhật badge
          const badge = document.getElementById('cart-count');
          if (badge) {
            badge.textContent = json.total_items;
            badge.style.display = 'block';
          }

          // b) Cập nhật header-cart-list
          const listContainer = document.querySelector('.scrollable-cart ul#header-cart-list');
          if (listContainer) {
            // remove placeholder nếu có
            const empty = listContainer.querySelector('.empty-cart');
            if (empty) empty.remove();

            // thử tìm <li data-key="…">
            let existing = listContainer.querySelector(`li[data-key="${json.item.key}"]`);
            if (existing) {
              // chỉ update số lượng
              const sm = existing.querySelector('small.text-muted');
              sm.textContent = `${json.item.price.toLocaleString('vi-VN')}₫ × ${json.item.quantity}`;
            } else {
              // tạo mới
              const li = document.createElement('li');
              li.className = 'd-flex align-items-center mb-2';
              li.dataset.key = json.item.key;
              li.innerHTML = `
                <img src="${json.item.image}"
                    width="50" class="me-2 rounded"
                    alt="${json.item.name}">
                <div class="flex-grow-1">
                  <div class="fw-semibold">${json.item.name}</div>
                  <small class="text-muted">
                    ${json.item.price.toLocaleString('vi-VN')}₫ × ${json.item.quantity}
                  </small>
                </div>`;
              // chèn li vào listContainer
              listContainer.appendChild(li);
            }
          }


          // c) Feedback nút
          btn.textContent = 'Đã thêm';
          setTimeout(() => btn.textContent = origText, 1500);

          // d) Notification
          if (window.showCartNotification) {
            window.showCartNotification(json.message, json.image);
          }
        } else {
          alert(json.message || 'Thêm thất bại, thử lại sau nhé!');
        }
      })
      .catch(err => {
        console.error(err);
        alert('Có lỗi xảy ra, xem console nhé.');
      });
    });
  });

  // 3) Option/value logic & giá/ảnh (giữ nguyên)
  document.querySelectorAll('.product-card').forEach(card => {
    const pid        = card.dataset.productId;
    const totalEl    = card.querySelector(`#total-price-${pid}`);
    const mainImg    = card.querySelector(`#main-img-${pid}`);
    const basePrice  = parseInt(totalEl.textContent.replace(/[^\d]/g,''),10);
    const items      = card.querySelectorAll('.option-item-show');
    const selected   = {};
    const formEl     = document.getElementById(`add-to-cart-form-${pid}`);
    const errorEl2   = document.getElementById(`option-error-${pid}`);
    const finalIn    = document.getElementById(`final-price-${pid}`);
    const imgIn      = document.getElementById(`selected-img-${pid}`);

    function updateTotal() {
      const sumExtra = Object.values(selected).reduce((a,b)=>a+b,0);
      const total    = basePrice + sumExtra;
      totalEl.textContent = total.toLocaleString('vi-VN') + '₫';
      finalIn.value       = total;
    }

    items.forEach(el => {
      el.addEventListener('click', () => {
        const typeId = el.dataset.typeId;
        const valId  = el.dataset.valId;
        const extra  = parseInt(el.dataset.extra)||0;

        card.querySelectorAll(`.option-item-show[data-type-id="${typeId}"]`)
            .forEach(sib=>sib.classList.remove('selected'));
        el.classList.add('selected');

        selected[typeId] = extra;
        document.getElementById(`option-input-${pid}-${typeId}`).value = valId;
        if (errorEl2) errorEl2.style.display = 'none';

        updateTotal();

        const groupEl = el.closest('.option-items-show');
        if (groupEl.dataset.firstGroup==='1' && el.dataset.img) {
          mainImg.src = el.dataset.img;
          imgIn.value = el.dataset.img;
        }
      });
    });

    formEl.addEventListener('submit', e => {
      const miss = Array.from(
        formEl.querySelectorAll(`input[id^="option-input-${pid}-"]`)
      ).some(i=>!i.value);
      if (miss) {
        e.preventDefault();
        if (errorEl2) errorEl2.style.display='block';
      }
    });
  });
});
</script>
@endpush


