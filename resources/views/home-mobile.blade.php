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

  <div class="slider-cont">
    {{-- 2) Intro text --}}
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
    @endif

    {{-- 3) Collection Slider --}}
    <div class="slider-full-width mb-5 ms-3 pt-5 pb-3">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h3 class="mb-0">{{ $home->collection_slider_title ?: 'Khám phá bộ sưu tập' }}</h3>
        <div class="nut-navi">
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
                <p class="mt-2 text-center the-p">{{ $s->text }}</p>
              </a>
            </div>
          @endforeach
        </div>
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

    {{-- 4B) Section Images --}}
    <div class="section-images mb-4">
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
    <div class="slider-product mb-5 ms-3">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h3 class="mb-0">{{ $home->product_slider_title ?: 'Sản phẩm nổi bật' }}</h3>
        <div class="nut-navi">
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
                <p class="text-center mb-1 the-p">{{ $ps->product->name }}</p>
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
  display: flex;                /* ngang thay vì dọc */
  flex-direction: row;
  gap: 12px;                    /* khoảng cách giữa các ảnh */
  overflow-x: auto;             /* cho phép cuộn ngang */
  scroll-snap-type: x mandatory;/* bật scroll-snap */
  padding-left: 1rem;           /* đệm trái để peek rõ hơn */
}

.section-images .section-img {
  flex: 0 0 85%;                /* mỗi ảnh rộng 85% container */
  scroll-snap-align: start;     /* snap mỗi ảnh khi scroll */
  position: relative;
}

/* Ẩn scrollbar (tuỳ chọn) */
.section-images::-webkit-scrollbar {
  display: none;
}

/* Ảnh chiếm full .section-img */
.section-images .section-img img {
  width: 100%;
  height: auto;
  border-radius: 10px;
  object-fit: cover;
}
  .nut-navi {
    display: none !important;
  }
  .slider-full-width.mb-5 {
  height: 500px;
  /* overflow: hidden;  */
  }

  /* Cho Swiper container và slide full 100% chiều cao */
  .slider-full-width .swiper,
  .slider-full-width .swiper-slide {
    height: 100%;
  }

  /* Ảnh trong slide chiếm full và cover */
  .slider-full-width .swiper-slide img {
    width: 100%;
    height: 90%;
    object-fit: cover;
  }
  .full-banner {
    width: 100%;
    aspect-ratio: 16 / 9;
    display: flex;
    justify-content: center;
    align-items: center;
    object-fit: cover;
    overflow: hidden;
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
    color: #333333;
    font-size: 1.2rem;
    font-weight: 500;
  }
</style>
@endpush
