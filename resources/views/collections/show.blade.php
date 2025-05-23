@extends('layouts.app')

{{-- ▼ BẮT ĐẦU CHÈN SIDEBAR --}}
@section('sidebar')
  @include('components.sidebar')
@endsection
{{-- ▲ KẾT THÚC CHÈN SIDEBAR --}}

@section('content')

@php
    $sector = $collection->sectors->first();
@endphp

<nav aria-label="breadcrumb">
  <ol class="breadcrumb bg-white px-0">
    {{-- Luôn có Danh mục đầu --}}
    <li class="breadcrumb-item">
      <a href="{{ route('sector.index') }}">Danh mục</a>
    </li>

    {{-- Chỉ show nếu có sector --}}
    @if($sector)
      <li class="breadcrumb-item">
        <a href="{{ route('sector.show', $sector->slug) }}">
          {{ $sector->name }}
        </a>
      </li>
    @endif

    {{-- Luôn show Collection làm active --}}
    <li class="breadcrumb-item active" aria-current="page">
      {{ $collection->name }}
    </li>
  </ol>
</nav>

<div class="py-4 tat-ca-san-pham-cont">
  <h1 class="mb-4">{{ $collection->name }}</h1>

  @if($collection->description)
    <p class="text-muted mb-4">{{ $collection->description }}</p>
  @endif

  <div class="row">
    @forelse($collection->products as $product)
      <div class="col-md-4 mb-4">
        <div class="card h-100">
          @if($product->img)
            <a href="{{ route('products.show', [
                      'slug'            => $product->slug,
                      'from_collection' => $collection->slug
                    ]) }}">

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

          <div class="card-body d-flex flex-column">
            @php
              // Kiểm tra đã favorite chưa: auth → DB, guest → session
              $isFav = auth()->check()
                ? auth()->user()->favorites->contains($product->id)
                : in_array($product->id, session('favorites', []));
            @endphp

            <h5 class="card-title d-flex justify-content-between align-items-center noi-chua-nut-favorites">
              <a href="{{ route('products.show', $product->slug) }}"
                class="text-decoration-none text-dark">
                {{ $product->name }}
              </a>
              <button type="button"
                      class="btn-favorite"
                      data-id="{{ $product->id }}">
                <i class="{{ $isFav ? 'fas text-danger' : 'far text-muted' }} fa-heart"></i>
              </button>
            </h5>

            <p class="card-text text-muted mb-2">
              Giá: <strong>{{ number_format($product->base_price,0,',','.') }}₫</strong>
            </p>

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