@extends('layouts.app')

@section('title', $classification->name)

@section('content')
<div class="container py-5">

  <div class="row">
    {{-- Left: gallery --}}
    <div class="col-md-6">
      <div class="d-flex">
        {{-- thumbnails --}}
        <div class="me-3" style="width: 80px;">
          @foreach($subImages as $img)
            <img src="{{ asset('storage/'.$img) }}"
                 class="img-fluid mb-2 thumbnail"
                 data-full="{{ asset('storage/'.$img) }}"
                 style="cursor:pointer; border:1px solid #ddd; border-radius:4px;">
          @endforeach
        </div>
        {{-- main image --}}
        <div class="flex-grow-1">
          <img id="mainImage"
               src="{{ asset('storage/'.$mainImage) }}"
               class="img-fluid rounded"
               style="max-height:500px; object-fit:contain; width:100%;">
        </div>
      </div>
    </div>

    {{-- Right: info --}}
    <div class="col-md-6">
      <h1>{{ $classification->name }}</h1>
      <h4 class="text-primary mb-4" id="price">
        {{ number_format($product->base_price + ($sizeOptions->first()->extra_price ?? 0)) }}₫
      </h4>

      {{-- 1) Chọn màu (variants) --}}
      <div class="mb-4">
        <h5>Chọn màu:</h5>
        <div class="d-flex">
          @foreach($colorVariants as $variant)
            <img src="{{ asset('storage/'.$variant->img) }}"
                 class="img-thumbnail me-2 color-option @if($variant->id==$classification->id) selected @endif"
                 data-id="{{ $variant->id }}"
                 data-img="{{ asset('storage/'.$variant->img) }}"
                 data-classification="{{ $variant->id }}"
                 style="width:60px; height:60px; object-fit:cover; cursor:pointer;">
          @endforeach
        </div>
      </div>

      {{-- 2) Chọn kích cỡ (options) --}}
      <div class="mb-4">
        <h5>Chọn kích cỡ:</h5>
        <div class="btn-group" role="group">
          @foreach($sizeOptions as $opt)
            <button type="button"
              class="btn btn-outline-secondary size-option @if($loop->first) active @endif"
              data-price="{{ $opt->extra_price }}"
              data-id="{{ $opt->id }}">
              {{ $opt->name }}
            </button>
          @endforeach
        </div>
      </div>

      {{-- Buttons --}}
      <button class="btn btn-primary btn-lg w-100 mb-2">Thêm giỏ hàng</button>
      <button class="btn btn-outline-secondary w-100">
        <i class="bi bi-heart"></i> Yêu thích
      </button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
  // 1) Thumbnails click → đổi main image
  document.querySelectorAll('.thumbnail').forEach(el => {
    el.onclick = () => {
      document.getElementById('mainImage').src = el.dataset.full;
    };
  });

  // 2) Chọn màu → reload trang sang variant khác
  document.querySelectorAll('.color-option').forEach(el => {
    el.onclick = () => {
      const id = el.dataset.classification;
      window.location.href = `{{ url('product') }}/${id}`;
    };
  });

  // 3) Chọn size → update giá
  const base = {{ $product->base_price }};
  function updatePrice() {
    let extra = 0;
    document.querySelectorAll('.size-option.active').forEach(btn => {
      extra = parseInt(btn.dataset.price);
    });
    document.getElementById('price').innerText = new Intl.NumberFormat('vi-VN').format(base+extra) + '₫';
  }
  document.querySelectorAll('.size-option').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.size-option').forEach(b=>b.classList.remove('active'));
      btn.classList.add('active');
      updatePrice();
    });
  });
})();
</script>
@endpush
