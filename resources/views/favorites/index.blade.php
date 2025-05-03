{{-- resources/views/favorites/index.blade.php --}}
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
document.addEventListener('DOMContentLoaded', ()=>{
  const csrf = document.querySelector('meta[name="csrf-token"]').content;

  // Toggle yêu thích: xóa ngay card và đổi icon
  document.querySelectorAll('.btn-fav-toggle').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const pid = btn.dataset.id;
      fetch(`/favorites/toggle/${pid}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrf,
          'Accept':       'application/json',
          'Content-Type': 'application/json'
        }
      })
      .then(res => res.json())
      .then(json => {
        const icon = btn.querySelector('i.fa-heart');
        const cardCol = btn.closest('.favorite-card');
        if(json.added){
          icon.classList.replace('far','fas');
        } else {
          icon.classList.replace('fas','far');
          if(cardCol) cardCol.remove();
        }
      })
      .catch(()=>{
        alert('Có lỗi, vui lòng thử lại.');
      });
    });
  });

  // Option + giá + ảnh + add-to-cart logic
  document.querySelectorAll('.product-card').forEach(card=>{
    const pid        = card.dataset.productId;
    const totalEl    = card.querySelector(`#total-price-${pid}`);
    const mainImg    = card.querySelector(`#main-img-${pid}`);
    const basePrice  = parseInt(totalEl.textContent.replace(/[^\d]/g,''),10);
    const items      = card.querySelectorAll('.option-item-show');
    const selected   = {};
    const form       = document.getElementById(`add-to-cart-form-${pid}`);
    const errorEl    = document.getElementById(`option-error-${pid}`);
    const finalInput = document.getElementById(`final-price-${pid}`);
    const imgInput   = document.getElementById(`selected-img-${pid}`);

    function updateTotal(){
      const sumExtra = Object.values(selected).reduce((a,b)=>a+b,0);
      const total    = basePrice + sumExtra;
      totalEl.textContent = total.toLocaleString('vi-VN') + '₫';
      finalInput.value   = total;
    }

    items.forEach(el=>{
      el.addEventListener('click', ()=>{
        const typeId = el.dataset.typeId;
        const valId  = el.dataset.valId;
        const extra  = parseInt(el.dataset.extra)||0;

        // Chọn option
        card.querySelectorAll(`.option-item-show[data-type-id="${typeId}"]`)
            .forEach(sib=>sib.classList.remove('selected'));
        el.classList.add('selected');

        selected[typeId] = extra;
        document.getElementById(`option-input-${pid}-${typeId}`).value = valId;
        errorEl.style.display = 'none';

        updateTotal();

        // Swap ảnh nếu nhóm đầu
        const groupEl = el.closest('.option-items-show');
        if(groupEl.dataset.firstGroup==='1' && el.dataset.img){
          mainImg.src    = el.dataset.img;
          imgInput.value = el.dataset.img;
        }
      });
    });

    form.addEventListener('submit', e=>{
      const missing = Array.from(
        form.querySelectorAll(`input[id^="option-input-${pid}"]`)
      ).some(i=>!i.value);
      if(missing){
        e.preventDefault();
        errorEl.style.display='block';
      }
    });
  });
});
</script>
@endpush
