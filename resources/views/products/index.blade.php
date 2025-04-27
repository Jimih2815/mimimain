@extends('layouts.app')

{{-- ▼ BẮT ĐẦU CHÈN SIDEBAR --}}
@section('sidebar')
  @include('components.sidebar')
@endsection
{{-- ▲ KẾT THÚC CHÈN SIDEBAR --}}

@section('content')
<div class="py-4 tat-ca-san-pham-cont">
  <h1 class="mb-4">Danh sách Sản phẩm</h1>

  <div class="row">
    @forelse($products as $product)
      <div class="col-md-4 mb-4">
        <div class="card h-100">
          @if($product->img)
            <a href="{{ route('products.show', $product->slug) }}">
              <img 
                src="{{ asset('storage/'.$product->img) }}" 
                class="card-img-top anh-chinh-san-pham" 
                alt="{{ $product->name }}">
            </a>
          @endif

          <div class="card-body d-flex flex-column">
            <h5 class="card-title">{{ $product->name }}</h5>
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
          </div>
        </div>
      </div>
    @empty
      <div class="col-12 text-center py-5">
        <p>Chưa có sản phẩm nào.</p>
      </div>
    @endforelse
  </div>

  {{-- Phân trang tùy chỉnh --}}
  @php
    $last    = $products->lastPage();
    $current = $products->currentPage();
  @endphp

  @if($last > 1)
    <nav class="pagination flex flex-1 sm:hidden nut-dieu-huong">
      @if($current <= 2)
        @foreach(range(1, min(3, $last)) as $i)
          <a href="{{ $products->url($i) }}" class="page-link {{ $current === $i ? 'active' : '' }}">
            {{ $i }}
          </a>
        @endforeach
        @if($last > 3)
          <a href="{{ $products->url($last) }}" class="page-link {{ $current === $last ? 'active' : '' }}">
            {{ $last }}
          </a>
        @endif

      @elseif($current === $last)
        <a href="{{ $products->url(1) }}" class="page-link">1</a>
        <a href="{{ $products->url($last - 1) }}" class="page-link">{{ $last - 1 }}</a>
        <a href="{{ $products->url($last) }}" class="page-link active">{{ $last }}</a>

      @elseif($current === $last - 1)
        <a href="{{ $products->url(1) }}" class="page-link">1</a>
        <a href="{{ $products->url($current - 1) }}" class="page-link">{{ $current - 1 }}</a>
        <a href="{{ $products->url($current) }}" class="page-link active">{{ $current }}</a>
        <a href="{{ $products->url($last) }}" class="page-link">{{ $last }}</a>

      @else
        <a href="{{ $products->url(1) }}" class="page-link">1</a>
        <a href="{{ $products->url($current - 1) }}" class="page-link">{{ $current - 1 }}</a>
        <a href="{{ $products->url($current) }}" class="page-link active">{{ $current }}</a>
        <a href="{{ $products->url($current + 1) }}" class="page-link">{{ $current + 1 }}</a>
        <a href="{{ $products->url($last) }}" class="page-link">{{ $last }}</a>
      @endif
    </nav>
  @endif
</div>
@endsection
