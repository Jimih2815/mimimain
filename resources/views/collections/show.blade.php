@extends('layouts.app')

{{-- Trang PC: muốn lưới sát mép phải màn hình hơn thì dùng container-fluid và bỏ gutter ngoài --}}
@section('main_container_class', 'container-fluid px-0')
@section('main_row_class', 'g-0')
@section('main_section_class', 'pe-0')

{{-- ▼ BẮT ĐẦU CHÈN SIDEBAR --}}
@section('sidebar')
  @include('components.sidebar')
@endsection
{{-- ▲ KẾT THÚC CHÈN SIDEBAR --}}

@section('content')
<div class="py-4 tat-ca-san-pham-cont px-3">
  <h1 class="mb-4">{{ $collection->name }}</h1>

  @if($collection->description)
    <p class="text-muted mb-4">{{ $collection->description }}</p>
  @endif

  <div class="row g-3">
    @forelse($collection->products as $product)
      <div class="col-6 col-md-4 col-lg-3">
        <div class="card h-100">
          @if($product->img)
            <a href="{{ route('products.show', $product->slug) }}">
              <img 
                src="{{ asset('storage/'.$product->img) }}" 
                class="card-img-top anh-chinh-san-pham" 
                alt="{{ $product->name }}">
            </a>
          @else
            <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                 style="height:200px">
              No Image
            </div>
          @endif

          <div class="card-body d-flex flex-column justify-content-between">
            @php
              // Kiểm tra đã favorite chưa: auth → DB, guest → session
              $isFav = auth()->check()
                ? auth()->user()->favorites->contains($product->id)
                : in_array($product->id, session('favorites', []));
            @endphp

            <h5  class="card-title d-flex justify-content-between align-items-start noi-chua-nut-favorites product-card-title">
              <a href="{{ route('products.show', $product->slug) }}"
                class="text-decoration-none text-dark product-title-link" >
                {{ $product->name }}
              </a>
              <button type="button"
                      class="btn-favorite"
                      data-id="{{ $product->id }}">
                <i class="{{ $isFav ? 'fas text-danger' : 'far text-muted' }} fa-heart"></i>
              </button>
            </h5>

            <p class="card-text mb-2"
               style="color: #b83232;">
               <strong>{{ number_format($product->base_price,0,',','.') }}₫</strong>
            </p>

            @if($product->optionValues->count())
            <div class="product-options d-block d-lg-none">
              @foreach(
                $product->optionValues
                        ->groupBy(fn($v) => $v->type->name)
                        as $typeName => $values
              )
                <div class="option-list mb-3">
                  <span class="option-type fw-semibold me-2">{{ $typeName }}:</span>
                  <div class="d-flex flex-row flex-nowrap overflow-auto option-items">
                    @foreach($values as $val)
                      <span class="option-item me-3">{{ $val->value }}</span>
                    @endforeach
                  </div>
                </div>
              @endforeach
            </div>

            <!-- <a href="{{ route('products.show', $product->slug) }}"
               class="btn btn-outline-secondary btn-sm w-100 mb-3 d-none d-lg-block">
              Xem chi tiết
            </a> -->
            @endif

            <form action="{{ route('cart.add', $product->id) }}"
                  method="POST"
                  class="mt-auto">
              @csrf
              <!-- <button type="submit" class="btn btn-primary w-100">
                Thêm vào giỏ hàng
              </button> -->
            </form>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12 text-center py-5">
        <p>Chưa có sản phẩm nào trong bộ sưu tập này.</p>
      </div>
    @endforelse
  </div>

  {{-- Phân trang nếu có paginate --}}
  @if(method_exists($collection->products, 'links'))
    <div class="mt-4 nut-dieu-huong">
      {{ $collection->products->links() }}
    </div>
  @endif
</div>

@include('partials.back-to-top-full')

@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const csrf = document.querySelector('meta[name="csrf-token"]').content;

  document.querySelectorAll('.btn-favorite').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;
      fetch(`/favorites/toggle/${id}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrf,
          'Accept':       'application/json',
          'Content-Type': 'application/json'
        },
      })
      .then(res => res.json())
      .then(json => {
        const icon = btn.querySelector('i.fa-heart');
        if (json.added) {
          icon.classList.replace('far', 'fas');
        } else {
          icon.classList.replace('fas', 'far');
        }
      });
    });
  });
});
</script>
@endpush
