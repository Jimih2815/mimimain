{{-- resources/views/news/show.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
    .show-prod-next, .show-prod-prev  {
        position: static !important;
        top: auto !important;
        margin: 0 !important;
        transform: none !important;
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        width: 3rem !important;
        height: 3rem !important;
        background-color: #4ab3af !important;
        border: 1px solid #4ab3af !important;
        border-radius: 50% !important;
        color: white !important;
        font-size: 1.5rem;
    }
</style>


  <div class="w-50 mx-auto mt-5 mb-5">
    <h1 class="mb-4">{{ $post->title }}</h1>
    <div class="prose mb-5">
      {!! $post->content !!}
    </div>


  </div>

  {{-- Slider sản phẩm liên quan --}}
    @if($post->collection && $post->collection->products->isNotEmpty())
      <div class="mb-5 news-related-wrapper w-100">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h3 class="mb-0">Sản phẩm liên quan</h3>
          <div class="d-flex gap-3">
            <button class="btn btn-outline-secondary me-2 show-prod-prev">
              <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button class="btn btn-outline-secondary show-prod-next">
              <i class="fa-solid fa-chevron-right"></i>
            </button>
          </div>
        </div>



        <div class="swiper show-product-swiper">
          <div class="swiper-wrapper">
            @foreach($post->collection->products as $product)
              <div class="swiper-slide">
                <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none">
                  <div class="ratio ratio-1x1 overflow-hidden">
                    <img src="{{ asset('storage/'.($product->sub_img[0] ?? $product->img)) }}"
                         class="w-100 h-100 object-fit-cover"
                         alt="{{ $product->name }}">
                  </div>
                  <p style="color:#333333; font-size:1.1rem;" class="mt-3 text-center fw-semibold mb-1">{{ $product->name }}</p>
                  <p style="color:#333333;" class="text-center mt-1">{{ number_format($product->base_price,0,',','.') }}₫</p>
                </a>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    @endif
@endsection
