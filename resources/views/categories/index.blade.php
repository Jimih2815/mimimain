@extends('layouts.app')

@section('title','Danh mục sản phẩm')

@section('content')
<div class="container py-4">

  {{-- Tầng 1: Nav-pills --}}
  <ul class="nav nav-pills mb-4" id="categoryTabs" role="tablist">
    @foreach($categories as $i => $cat)
      <li class="nav-item" role="presentation">
        <button
          class="nav-link {{ $i === 0 ? 'active' : '' }}"
          id="tab-{{ $cat->slug }}"
          data-bs-toggle="pill"
          data-bs-target="#content-{{ $cat->slug }}"
          type="button"
          role="tab"
          aria-controls="content-{{ $cat->slug }}"
          aria-selected="{{ $i === 0 ? 'true' : 'false' }}"
        >
          {{ $cat->name }}
        </button>
      </li>
    @endforeach
  </ul>

  {{-- Tầng 2: Tab content --}}
  <div class="tab-content" id="categoryTabsContent">
    @foreach($categories as $i => $cat)
      <div
        class="tab-pane fade {{ $i === 0 ? 'show active' : '' }}"
        id="content-{{ $cat->slug }}"
        role="tabpanel"
        aria-labelledby="tab-{{ $cat->slug }}"
      >
        @if($cat->products->isEmpty())
          <p class="text-muted">Chưa có sản phẩm cho mục này.</p>
        @else
          <div class="row">
            @foreach($cat->products as $product)
              <div class="col-md-3 mb-4">
                <div class="card h-100">
                  <img src="{{ $product->img }}" class="card-img-top" alt="{{ $product->name }}">
                  <div class="card-body d-flex flex-column">
                    <h5 class="card-title">{{ $product->name }}</h5>
                    <p class="fw-bold mt-auto">{{ number_format($product->base_price,0,',','.') }}₫</p>
                    <a href="{{ route('products.show',['slug'=>$product->slug]) }}"
                       class="btn btn-sm btn-primary mt-2">
                      Xem chi tiết
                    </a>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    @endforeach
  </div>

</div>
@endsection
