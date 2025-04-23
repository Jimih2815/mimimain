
@extends('layouts.app')

@section('content')
<div class="container py-5">
  {{-- Tiêu đề bộ sưu tập --}}
  <h1 class="mb-4">{{ $collection->name }}</h1>

  {{-- Mô tả nếu có --}}
  @if($collection->description)
    <p class="lead">{{ $collection->description }}</p>
  @endif

  {{-- Lưới sản phẩm --}}
  <div class="row gy-4">
    @forelse($collection->products as $product)
      <div class="col-sm-6 col-md-4 col-lg-3">
        {{-- Card đơn giản, nếu không có components/product-card --}}
        <div class="card h-100">
          @if($product->img)
            <img src="{{ asset('storage/'.$product->img) }}"
                 class="card-img-top"
                 alt="{{ $product->name }}">
          @else
            <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                 style="height:200px">
              No Image
            </div>
          @endif
          <div class="card-body d-flex flex-column">
            <h5 class="card-title">{{ $product->name }}</h5>
            <p class="card-text mb-2 text-truncate">{{ $product->description }}</p>
            <p class="fw-bold mb-3">{{ number_format($product->base_price,0,',','.') }}₫</p>
            <a href="{{ route('products.show', $product->slug) }}"
               class="btn btn-primary mt-auto">
              Xem chi tiết
            </a>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <p class="text-center">Chưa có sản phẩm nào trong bộ sưu tập này.</p>
      </div>
    @endforelse
  </div>
</div>
@endsection
