{{-- resources/views/products/index.blade.php --}}
@extends('layouts.app')

{{-- ▼ SIDEBAR --}}
@section('sidebar')
  @include('components.sidebar')
@endsection
{{-- ▲ SIDEBAR --}}

@section('content')
<div class="py-4 tat-ca-san-pham-cont">
  <h1 class="mb-4">Danh sách Sản phẩm</h1>

  <div class="row">
    @forelse($products as $product)
      <div class="col-md-4 mb-4">
        <div class="card h-100 product-card" data-product-id="{{ $product->id }}">
          @if($product->img)
            <a href="{{ route('products.show', $product->slug) }}">
              <img
                src="{{ asset('storage/'.$product->img) }}"
                class="card-img-top product-main-img"
                alt="{{ $product->name }}"
                id="main-img-{{ $product->id }}">
            </a>
          @endif

          <div class="card-body d-flex flex-column">
            <h5 class="card-title">{{ $product->name }}</h5>

            <p class="card-text text-muted mb-2">
              Giá: <strong id="total-price-{{ $product->id }}">{{ number_format($product->base_price,0,',','.') }}₫</strong>
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
                      <div class="option-item-show me-3"
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
                !!! Vui lòng chọn tất cả tuỳ chọn.
              </div>

              {{-- 3) KÈM thêm 2 hidden-input mới --}}
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
                      class="btn btn-primary w-100">
                Thêm vào giỏ hàng
              </button>
            </form>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12 text-center py-5">
        <p>Chưa có sản phẩm nào.</p>
      </div>
    @endforelse
  </div>

  {{-- Phân trang --}}
  <div class="mt-4 nut-dieu-huong">
    {{ $products->links() }}
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', ()=>{
  const csrf = document.querySelector('meta[name="csrf-token"]').content;

  // === Toggle favorite (giữ nguyên) ===
  document.querySelectorAll('.btn-favorite').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const id = btn.dataset.id;
      fetch(`/favorites/toggle/${id}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrf,
          'Accept':       'application/json',
          'Content-Type': 'application/json'
        }
      })
      .then(r => r.json())
      .then(json => {
        const icon = btn.querySelector('i.fa-heart');
        if(json.added) icon.classList.replace('far','fas');
        else           icon.classList.replace('fas','far');
      });
    });
  });

  // === Option logic + update giá + swap ảnh + set hidden-inputs ===
  document.querySelectorAll('.product-card').forEach(card=>{
    const pid       = card.dataset.productId;
    const totalEl   = card.querySelector(`#total-price-${pid}`);
    const mainImg   = card.querySelector(`#main-img-${pid}`);
    const basePrice = parseInt(totalEl.textContent.replace(/[^\d]/g,''),10);
    const items     = card.querySelectorAll('.option-item-show');
    const selected  = {};
    const form      = document.getElementById(`add-to-cart-form-${pid}`);
    const errorEl   = document.getElementById(`option-error-${pid}`);
    const finalInput= document.getElementById(`final-price-${pid}`);
    const imgInput  = document.getElementById(`selected-img-${pid}`);

    function updateTotal(){
      const sumExtra = Object.values(selected).reduce((a,b)=>a+b,0);
      const total    = basePrice + sumExtra;
      totalEl.textContent = total.toLocaleString('vi-VN') + '₫';
      finalInput.value   = total;         // cập nhật giá gửi về
    }

    items.forEach(el=>{
      el.addEventListener('click', ()=>{
        const typeId = el.dataset.typeId;
        const valId  = el.dataset.valId;
        const extra  = parseInt(el.dataset.extra) || 0;

        // Bỏ chọn đồng nhóm, chọn thằng này
        card.querySelectorAll(`.option-item-show[data-type-id="${typeId}"]`)
            .forEach(sib=>sib.classList.remove('selected'));
        el.classList.add('selected');

        // Lưu giá extra & giá ảnh
        selected[typeId] = extra;
        document.getElementById(`option-input-${pid}-${typeId}`).value = valId;
        errorEl.style.display = 'none';

        updateTotal();

        // Swap ảnh nếu nhóm đầu && có ảnh
        const groupEl = el.closest('.option-items-show');
        if(groupEl.dataset.firstGroup==='1' && el.dataset.img){
          mainImg.src    = el.dataset.img;
          imgInput.value = el.dataset.img;  // cập nhật ảnh gửi về
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
