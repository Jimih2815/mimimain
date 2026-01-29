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
           class="nut-dau-trang mx-auto">
          {{ $home->pre_banner_button_text }}  <i class="fa-solid fa-angles-right"></i>
        </a>
      @endif
    </div>
  @endif

  {{-- 1) Banner (Mobile ưu tiên ảnh dọc nếu có) --}}
@php
  $bannerPath = $home->banner_image_mobile ?: $home->banner_image;
@endphp

@if($bannerPath)
  <div class="full-banner position-relative mb-">
    <img src="{{ asset('storage/'.$bannerPath) }}"
         alt="Home Banner"
         class="w-100 mobile-banner-img">
    @if($home->show_button && $home->buttonCollection)
      <a href="{{ route('collections.show', $home->buttonCollection->slug) }}"
         class="btn btn-lg btn-primary nut-banner position-absolute top-50 start-50 translate-middle">
        {{ $home->button_text }}
      </a>
    @endif
  </div>
@endif

<div class="slider-cont">
    <!-- {{-- 2) Intro text --}}
    @if($home->intro_text)
      <div class="text-center mb-4 ms-3 me-3">
        <p class="lead">{{ $home->intro_text }}</p>
        @if($home->introButtonCollection)
          <a href="{{ route('collections.show', $home->introButtonCollection->slug) }}"
             class="btn-mimi nut-vang mx-auto">
            {{ $home->intro_button_text }}
          </a>
        @endif
      </div>
    @endif -->
{{-- 3) Collection (xếp dọc, không slider) --}}
<div class="slider-full-width mb-5 ms-0 me-0 pt-5 pb-3 px-0">
  <div class="d-flex justify-content-between px-3 align-items-center mb-2">
    <h3 class="mb-0 tieu-de">{{ $home->collection_slider_title ?: 'Khám phá bộ sưu tập' }}</h3>
  </div>

  <div class="collection-stack">
    @foreach($sliders as $s)
      <div class="swiper-slide swiper-slide-active collection-stack-item">
        <a href="{{ route('collections.show', $s->collection->slug) }}" class="d-block">
          <img src="{{ asset('storage/'.$s->image) }}"
               alt="{{ $s->text }}"
               class="w-100 collection-stack-img">
          <p class="the-p collection-stack-caption">{{ $s->text }}</p>
        </a>
      </div>
    @endforeach
  </div>
</div>

{{-- 4A) Khám phá bộ sưu tập --}}
    @if($home->collection_section_title)
      <div class="text-center mb-4 ms-3 me-3 pt-4">
        <h2 class="">{{ $home->collection_section_title }}</h2>
        @if($home->collectionSectionCollection)
          <a href="{{ route('collections.show', $home->collectionSectionCollection->slug) }}"
             class="btn-mimi nut-do mx-auto">
            {{ $home->collection_section_button_text }}
          </a>
        @endif
      </div>
    @endif

  {{-- 4B) Section Images slider tự động --}}
  <div class="swiper section-images-swiper mb-4 ms-1 me-1">
    <div class="swiper-wrapper">
      @foreach($sectionImages as $img)
        <div class="swiper-slide section-img">
          <a class ="img-cont" href="{{ $img->collection
                        ? route('collections.show', $img->collection->slug)
                        : '#' }}">
            <img src="{{ asset('storage/'.$img->image) }}"
                alt="Section Image {{ $loop->iteration }}"
                class="w-100 rounded">
          </a>
        </div>
      @endforeach
    </div>
  </div>

    

    {{-- 5) Product Slider --}}
    <div class="slider-home-product mb-5 ms-1 me-1">
      <div class="d-flex justify-content-between align-items-center ms-2 mb-2">
        <h3 class="mb-0">{{ $home->product_slider_title ?: 'Sản phẩm nổi bật' }}</h3>
        <div class="nut-navi">
          <button class="btn btn-outline-secondary me-2 home-product-prev">
             <i class="bi bi-chevron-left fs-4"></i>
          </button>
          <button class="btn btn-outline-secondary home-product-next">
            <i class="bi bi-chevron-right fs-4"></i>
          </button>
        </div>
      </div>
      <div class="swiper home-product-swiper">
        <div class="swiper-wrapper">
          @foreach($productSliders as $ps)
            <div class="swiper-slide">
              <a href="{{ route('products.show', $ps->product->slug) }}">
                <img src="{{ asset('storage/'.$ps->image) }}"
                     alt="{{ $ps->product->name }}"
                     class="w-100 rounded mb-2">
                <p class="text-center mb-1 the-p-2">{{ $ps->product->name }}</p>
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
  /* === Banner full màn hình dọc (Mobile) === */
  .full-banner {
    width: 100%;
    height: 75svh; /* iOS-friendly */
    min-height: 75vh;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  .mobile-banner-img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover;
  }

  /* Ẩn navi (giữ cho đồng bộ layout cũ) */
  .nut-navi { display: none !important; }

  /* === Collection stack (thay slider) === */
.collection-stack{
  display:flex;
  flex-direction:column;
  /* gap:22px; */
  padding:0;              
  margin:0;
}
.collection-stack-item a{
  position:relative;
  display:block;
}
/* lớp tối gradient để chữ trắng luôn đọc được dù ảnh nhiều màu / trắng */
.collection-stack-item a::after{
  content:"";
  position:absolute;
  left:0; right:0; bottom:0;
  height:45%;
  background:linear-gradient(to top, rgba(0,0,0,.65), rgba(0,0,0,0));
  pointer-events:none;
  z-index:1;
}
.collection-stack-img{
  width:100%;
  height:66svh;       /* ~2/3 màn hình */
  max-height:66vh;
  object-fit:cover;
  border-radius:0;    /* bỏ bo góc */
  display:block;
}
.collection-stack-caption{
  position:absolute;
  left:0; right:0;
  bottom:14px;
  margin:0;
  padding:0 14px;
  text-align:center;
  color:#fff;
  font-weight:800;
  z-index:2; /* nằm trên gradient */
  text-shadow:
    0 2px 10px rgba(0,0,0,.85),
    0 0 2px rgba(0,0,0,.95);
}

/* Section Images (Swiper) */

  .section-images {
    display: flex;
    flex-direction: row;
    gap: 12px;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
  }
  .section-images .section-img {
    flex: 0 0 85%;
    scroll-snap-align: start;
    position: relative;
  }
  .section-images::-webkit-scrollbar { display: none; }
  .section-images .section-img img {
    width: 100% !important;
    height: 100% !important;
    border-radius: 10px;
    object-fit: cover;
  }

  .lead {
    font-size: 1.1rem;
    font-weight: 400;
  }
  .nut-dau-trang { 
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 5px;
    color: #4ab3af;
    font-weight: 700;
  }
  .nut-dau-trang .fa-angles-right {
    font-size: 0.8rem;
    padding-top: 1.5px;
  }
  .the-p {
    text-align: justify;
    color: white;
    font-size: 1.8rem;
    font-weight: 500;
    padding-bottom: 1.5rem;
  }

  .img-cont {
    width: 100%;
    display: flex;
    aspect-ratio: 5 / 3;
    height: 100%;
    object-fit: cover;
    overflow: hidden;
    justify-content: center;
    align-items: center;
  }
  .tieu-de {
    font-size: 1.8rem;
    padding-bottom: 1rem;
  }
</style>
@endpush

