{{-- resources/views/news/index.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
    .news-next,
    .news-prev {
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
  <h1 class="mb-5" style="color:#b83232;">Tin tức mới nhất</h1>

  @foreach($posts as $post)
    <div class="d-flex mb-4 border-bottom pb-3">
      {{-- Thumbnail --}}
      @if($post->thumbnail)
        <img src="{{ asset('storage/'.$post->thumbnail) }}"
             class="me-3"
             style="width:200px; height:200px; object-fit:cover;">
      @else
        <div class="bg-secondary me-3"
             style="width:200px; height:200px;"></div>
      @endif

      <div>
        <a href="{{ route('news.show', $post->slug) }}"
           class="h5 text-decoration-none text-dark">
          {{ $post->title }}
        </a>
        <div class="text-muted small">
          {{ $post->created_at->format('d/m/Y') }}
        </div>
      </div>
    </div>
  @endforeach

  {{ $posts->links() }}
</div>

{{-- ===== Slider “News” ===== --}}
@if(isset($selectedCollection) && $selectedCollection->products->isNotEmpty())
  <div class="w-100 news-slider-wrapper mb-5 mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="mb-0">Top Treding Hiện Nay</h3>
      <div class="d-flex align-items-center gap-3">
        <button class="btn btn-outline-secondary me-2 news-prev">
          <i class="fa-solid fa-chevron-left"></i>
        </button>
        <button class="btn btn-outline-secondary news-next">
          <i class="fa-solid fa-chevron-right"></i>
        </button>
      </div>
    </div>


    <div class="swiper news-swiper">
      <div class="swiper-wrapper">
        @foreach($selectedCollection->products as $product)
          <div class="swiper-slide">
            <a class="text-decoration-none" href="{{ route('products.show', $product->slug) }}">
              <div class="ratio ratio-1x1 overflow-hidden">
                <img src="{{ asset('storage/'.($product->sub_img[0] ?? $product->img)) }}"
                     class="w-100 h-100 object-fit-cover object-position-center"
                     alt="{{ $product->name }}">
              </div>
              <p style="color:#333333; font-size:1.1rem;" class="mt-3 mb-1 text-center fw-semibold">{{ $product->name }}</p>
              <p style="color:#333333;" class="text-center">{{ number_format($product->base_price,0,',','.') }}₫</p>
            </a>
          </div>
        @endforeach
      </div>
    </div>
  </div>
@endif
@endsection


