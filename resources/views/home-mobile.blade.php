{{-- resources/views/home-mobile.blade.php --}}
@extends('layouts.app-mobile')

@section('title','Trang Chủ Mobile')

@section('content')
  {{-- 0) Pre-banner --}}
  @if($home->pre_banner_title)
    <div class="pre-banner text-center py-4 bg-light w-100">
      <h5>{{ $home->pre_banner_title }}</h5>
      @if($home->preBannerCollection)
        <a href="{{ route('collections.show',$home->preBannerCollection->slug) }}"
           class="btn btn-primary mt-2">
          {{ $home->pre_banner_button_text }}
        </a>
      @endif
    </div>
  @endif

  {{-- 1) Banner động --}}
  <div class="full-banner position-relative mb-4">
    <img src="{{ asset('storage/'.$home->banner_image) }}"
         alt="Home Banner"
         class="w-100 mobile-banner-img">
    @if($home->show_button && $home->buttonCollection)
      <a href="{{ route('collections.show', $home->buttonCollection->slug) }}"
         class="btn btn-lg btn-primary nut-banner position-absolute top-50 start-50 translate-middle">
        {{ $home->button_text }}
      </a>
    @endif
  </div>

  <div class="px-3">
    {{-- 2) Intro text --}}
    @if($home->intro_text)
      <div class="text-center mb-4">
        <p class="lead">{{ $home->intro_text }}</p>
        @if($home->introButtonCollection)
          <a href="{{ route('collections.show', $home->introButtonCollection->slug) }}"
             class="btn btn-outline-primary">
            {{ $home->intro_button_text }}
          </a>
        @endif
      </div>
    @endif

    {{-- 3) Collection Slider --}}
    <div class="slider-full-width mb-5">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h3 class="mb-0">{{ $home->collection_slider_title ?: 'Khám phá bộ sưu tập' }}</h3>
        <div>
          <button class="btn btn-outline-secondary me-2 swiper-button-prev">
            <i class="bi bi-chevron-left fs-4"></i>
          </button>
          <button class="btn btn-outline-secondary swiper-button-next">
            <i class="bi bi-chevron-right fs-4"></i>
          </button>
        </div>
      </div>
      <div class="swiper collection-swiper">
        <div class="swiper-wrapper">
          @foreach($sliders as $s)
            <div class="swiper-slide">
              <a href="{{ route('collections.show', $s->collection->slug) }}">
                <img src="{{ asset('storage/'.$s->image) }}"
                     alt="{{ $s->text }}"
                     class="w-100 rounded">
                <p class="mt-2 text-center">{{ $s->text }}</p>
              </a>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- 4) Section Images --}}
    <div class="section-images mb-5">
      @foreach($sectionImages as $img)
        <div class="section-img">
          <a href="{{ $img->collection
                          ? route('collections.show', $img->collection->slug)
                          : '#' }}">
            <img src="{{ asset('storage/'.$img->image) }}"
                 alt="Section Image {{ $loop->iteration }}"
                 class="w-100 rounded">
          </a>
        </div>
      @endforeach
    </div>

    {{-- 5) Product Slider --}}
    <div class="slider-product mb-5">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h3 class="mb-0">{{ $home->product_slider_title ?: 'Sản phẩm nổi bật' }}</h3>
        <div>
          <button class="btn btn-outline-secondary me-2 product-slider-prev">
            <i class="bi bi-chevron-left fs-4"></i>
          </button>
          <button class="btn btn-outline-secondary product-slider-next">
            <i class="bi bi-chevron-right fs-4"></i>
          </button>
        </div>
      </div>
      <div class="swiper product-swiper">
        <div class="swiper-wrapper">
          @foreach($productSliders as $ps)
            <div class="swiper-slide">
              <a href="{{ route('products.show', $ps->product->slug) }}">
                <img src="{{ asset('storage/'.$ps->image) }}"
                     alt="{{ $ps->product->name }}"
                     class="w-100 rounded mb-2">
                <p class="text-center mb-1">{{ $ps->product->name }}</p>
                <p class="text-center text-danger fw-bold">
                  {{ number_format($ps->product->base_price,0,',','.') }}₫
                </p>
              </a>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
@endsection

@push('styles')
<style>
  /* Section Images xếp dọc */
  .section-images {
    display: flex;
    flex-direction: column;
  }
  .section-images .section-img + .section-img {
    margin-top: 1rem;
  }
</style>
@endpush
