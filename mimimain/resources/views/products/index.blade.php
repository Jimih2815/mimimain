@extends('layouts.app')

@section('title', 'Tất cả sản phẩm')

@section('content')
<div class="container py-5">
  <h1 class="mb-4">Tất cả sản phẩm</h1>

  @if($items->isEmpty())
    <div class="alert alert-info">Chưa có sản phẩm nào.</div>
  @else
    <div class="row g-4">
      @foreach($items as $item)
        @php
          $base   = $item->product->base_price;
          $extras = $item->options->pluck('extra_price')->all();
          if (empty($extras)) {
            $min = $max = $base;
          } else {
            $min = $base + min($extras);
            $max = $base + max($extras);
          }
        @endphp

        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card h-100">
            <a href="{{ route('products.show', $item->id) }}">
              <img
                src="{{ asset('storage/'.$item->img) }}"
                class="card-img-top"
                style="height:200px; object-fit:cover;"
                alt="{{ $item->name }}"
              >
            </a>
            <div class="card-body d-flex flex-column">
              <h5 class="card-title">
                <a href="{{ route('products.show', $item->id) }}"
                   class="text-dark text-decoration-none">
                  {{ $item->name }}
                </a>
              </h5>
              <p class="card-text mt-auto fw-bold text-primary">
                {{ number_format($min,0,',','.') }}₫
                @if($min !== $max)
                  &ndash; {{ number_format($max,0,',','.') }}₫
                @endif
              </p>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>
@endsection
