{{-- resources/views/products/show.blade.php --}}
@extends('layouts.app')

@section('content')

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
      <h2>{{ $product->name }}</h2>
      <p class="text-muted">{{ $product->description }}</p>

      {{-- Tổng giá cập nhật --}}
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
            <div class="d-flex flex-row flex-nowrap option-items-show mb-2">
              @foreach($type->values as $val)
                <div class="option-item-show"
                     data-type-id="{{ $type->id }}"
                     data-val-id="{{ $val->id }}"
                     data-extra="{{ $val->extra_price }}">
                  {{ $val->value }}
                  @if($val->extra_price)
                    (+{{ number_format($val->extra_price,0,',','.') }}₫)
                  @endif
                </div>
              @endforeach
            </div>
            <input type="hidden"
                   name="options[{{ $type->id }}]"
                   id="option-input-{{ $type->id }}"
                   required>
          </div>
        @empty
          <p class="text-warning"></p>
        @endforelse
        {{-- Thông báo lỗi chọn option --}}
        <div id="option-error" class="text-danger small mb-2" style="display:none;">
          Vui lòng chọn các tuỳ chọn.
        </div>
        <button type="submit" class="btn-them-gio-trang-chi-tiet">
          Thêm vào giỏ hàng
        </button>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // --- Thumb click đổi ảnh chính và điều chỉnh chiều cao container ---
  const mainImg        = document.getElementById('main-product-img');
  const thumbs         = document.querySelectorAll('.thumb-item');
  const thumbsContainer= document.querySelector('.thumbs-container');

  function adjustThumbsHeight() {
    if (thumbsContainer && mainImg) {
      thumbsContainer.style.height = mainImg.clientHeight + 'px';
    }
  }

  mainImg.addEventListener('load', adjustThumbsHeight);
  window.addEventListener('resize', adjustThumbsHeight);
  if (mainImg.complete) adjustThumbsHeight();

  thumbs.forEach(el => {
    el.addEventListener('click', () => {
      thumbs.forEach(t => t.classList.remove('selected'));
      el.classList.add('selected');
      mainImg.src = el.dataset.src;
    });
  });

  // --- Logic lựa chọn option và cập nhật giá ---
  const basePrice = {{ $product->base_price }};
  const totalEl   = document.getElementById('total-price');
  const items     = document.querySelectorAll('.option-item-show');
  const selected  = {};

  //  Bắt buộc chọn hết
  const form     = document.getElementById('add-to-cart-form');
  const errorEl  = document.getElementById('option-error');

  form.addEventListener('submit', (e) => {
    const inputs = form.querySelectorAll('input[id^="option-input-"]');
    let missing = Array.from(inputs).some(inp => !inp.value);
    if (missing) {
      e.preventDefault();
      errorEl.style.display = 'block';
    }
  });

  function updateTotal() {
    const sumExtra = Object.values(selected).reduce((a, b) => a + b, 0);
    const total    = basePrice + sumExtra;
    totalEl.textContent = total.toLocaleString('vi-VN') + '₫';
  }

  items.forEach(el => {
    el.addEventListener('click', () => {
      const typeId = el.dataset.typeId;
      const valId  = el.dataset.valId;
      const extra  = parseInt(el.dataset.extra) || 0;

      document
        .querySelectorAll(`.option-item-show[data-type-id="${typeId}"]`)
        .forEach(sib => sib.classList.remove('selected'));

      el.classList.add('selected');
      selected[typeId] = extra;
      document.getElementById(`option-input-${typeId}`).value = valId;
      updateTotal();
      errorEl.style.display = 'none'; // ẩn lỗi khi chọn
      updateTotal();
    });
  });

});
</script>
@endpush
