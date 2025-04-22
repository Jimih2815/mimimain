@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h2 class="mb-4">Tất cả sản phẩm</h2>
  <div class="row">
    @foreach($products as $product)
      <div class="col-md-4 mb-3">
        <div class="card h-100">
          <img src="{{ $product->img }}" class="card-img-top" alt="{{ $product->name }}">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title">{{ $product->name }}</h5>
            <p class="card-text">{{ Str::limit($product->description, 100) }}</p>
            <p class="fw-bold">{{ number_format($product->base_price, 0, ',', '.') }}₫</p>
            <a href="{{ route('products.show', ['slug' => $product->slug]) }}"
               class="btn btn-primary mt-auto">
              Xem chi tiết
            </a>
          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection
