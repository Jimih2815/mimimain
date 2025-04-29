{{-- resources/views/home.blade.php --}}
@extends('layouts.app')

@section('title','Trang Chủ')

@section('content')
  {{-- Phần trước banner --}}
    @if($home->pre_banner_title)
      <div class="text-center py-4 bg-light container-trang-chu-truoc-banner">
        <h5>{{ $home->pre_banner_title }}</h5>
        @if($home->preBannerCollection)
          <a href="{{ route('collections.show',$home->preBannerCollection->slug) }}"
            class="btn mt-2 btn-trang-chu-truoc-banner">
            {{ $home->pre_banner_button_text }}
          </a>
        @endif
      </div>
    @endif
  {{-- 1) Banner động full-screen --}}
  <div class="position-relative" style="width:100%; height:100vh; overflow:hidden;">
    <img src="{{ asset('storage/'.$home->banner_image) }}"
         alt="Home Banner"
         style="width:100vw; height:100vh; object-fit:cover; object-position:center;">

    {{-- 1.a) Central button, chỉ hiển thị khi admin bật và chọn Collection --}}
    <div class="position-absolute top-80 start-50 translate-middle">
      @if($home->show_button && $home->buttonCollection)
        <a href="{{ route('collections.show', $home->buttonCollection->slug) }}"
           class="btn btn-lg btn-primary nut-banner">
          {{ $home->button_text }}
        </a>
      @endif
    </div>
  </div>

  {{-- 2) Container chính của nội dung --}}
  <div class="container">
    {{-- PHẦN KHÁM PHÁ THẾ GIỚI QUÀ TẶNG --}}
    {{-- 2) Phần Khởi Đầu --}}
    @if($home->intro_text)
      <div class="lead text-center mb-4 mt-4">
        <p class="lead text-center mb-2">{{ $home->intro_text }}</p>
        @if($home->introButtonCollection)
          <a href="{{ route('collections.show', $home->introButtonCollection->slug) }}"
            class="nut-xem-chi-tiet-trang-chu fw-semibold">
            {{ $home->intro_button_text }}
          </a>
        @endif
      </div>
    @endif
    <!-- <div class="text-center mb-5">
      <a href="#" class="nut-xem-chi-tiet-trang-chu fw-semibold">Xem chi tiết →</a>
    </div> -->

    {{-- 3) Hai ảnh chữ nhật cạnh nhau --}}
    <!-- <div class="row g-0 mb-5">
      <div class="col-6 pe-1">
        <img src="https://hoanghamobile.com/.../anh-mau-xanh-duong-42.jpg"
             class="img-fluid w-100" alt="">
      </div>
      <div class="col-6 ps-1">
        <img src="https://hoanghamobile.com/.../anh-mau-xanh-duong-42.jpg"
             class="img-fluid w-100" alt="">
      </div>
    </div> -->

    {{-- 4) Collection Slider --}}
<div class="slider-full-width mb-5">

  {{-- Tiêu đề + Prev/Next ở cùng hàng --}}
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">{{ $home->collection_slider_title ?? 'Tiêu đề Slide' }}</h3>
    <div class="d-flex align-items-center">
      <button type="button" class="btn btn-outline-secondary me-2 swiper-button-prev">
        <i class="fa-solid fa-chevron-left fs-4"></i>
      </button>
      <button type="button" class="btn btn-outline-secondary swiper-button-next">
        <i class="fa-solid fa-chevron-right fs-4"></i>
      </button>
    </div>
  </div>

  {{-- Chỉ giữ mỗi container Swiper --}}
  <div class="swiper collection-swiper">
    <div class="swiper-wrapper overflow-auto" style="scroll-snap-type: x mandatory;">
      @foreach($sliders as $s)
        <div class="swiper-slide" style="scroll-snap-align: start;">
          <div class="position-relative overflow-hidden" style="width:500px; aspect-ratio:3/4">
            <a href="{{ route('collections.show', $s->collection->slug) }}">
              <img
                src="{{ asset('storage/'.$s->image) }}"
                alt="{{ $s->text }}"
                class="w-100 h-100 object-fit-cover object-position-center"
              >
            </a>
          </div>
          <p class="mt-4 text-center fw-semibold">{{ $s->text }}</p>
        </div>
      @endforeach
    </div>
  </div>
</div>




    {{-- PHẦN KHÁM PHÁ BỘ SƯU TẬP --}}
    @if($home->collection_section_title)
      <div class="text-center mb-5">
        <h2 class="pb-4 mb-0 mt-5">{{ $home->collection_section_title }}</h2>
        @if($home->collectionSectionCollection)
          <a href="{{ route('collections.show', $home->collectionSectionCollection->slug) }}"
            class="nut-bosuutap fw-semibold">
            {{ $home->collection_section_button_text }}
          </a>
        @endif
      </div>
    @endif

    {{-- 6) Hai ảnh Section --}}
    <div class="section-images mb-5 d-flex gap-3">
      @foreach($sectionImages as $img)
        <div class="flex-fill">
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

    {{-- 7) Product Slider với navigation bên ngoài --}}
<div class="slider-product mb-5">
  {{-- Tiêu đề + Prev/Next giống Collection --}}
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">{{ $home->product_slider_title ?? 'Tiêu đề Sản phẩm' }}</h3>
    <div class="d-flex align-items-center">
      <button type="button" class="btn btn-outline-secondary me-2 product-slider-button-prev">
        <i class="fa-solid fa-chevron-left fs-4"></i>
      </button>
      <button type="button" class="btn btn-outline-secondary product-slider-button-next">
        <i class="fa-solid fa-chevron-right fs-4"></i>
      </button>
    </div>
  </div>

  {{-- Swiper container --}}
  <div class="swiper product-swiper overflow-visible">
    <div class="swiper-wrapper">
      @foreach($productSliders as $ps)
        <div class="swiper-slide" style="width:400px">
          <a href="{{ route('products.show', $ps->product->slug) }}">
            <div class="ratio ratio-1x1 overflow-hidden">
              <img src="{{ asset('storage/'.$ps->image) }}"
                   alt="{{ $ps->product->name }}"
                   class="w-100 h-100 object-fit-cover object-position-center">
            </div>
          </a>
          <p class="mt-3 text-center fw-semibold">{{ $ps->product->name }}</p>
          <p class="text-center">
            {{ number_format($ps->product->base_price,0,',','.') }}₫
          </p>
        </div>
      @endforeach
    </div>
    {{-- Bỏ hẳn <div class="swiper-button-prev"></div> và <div class="swiper-button-next"></div> ở đây --}}
    <!-- <div class="swiper-pagination"></div> -->
  </div>
</div>

    {{-- 8) About section --}}
    <div class="container text-center py-5">
      <h2 class="mb-4">{{ $home->about_title }}</h2>
      <p class="mx-auto" style="max-width:720px">{{ $home->about_text }}</p>
    </div>
  </div>
@endsection

@push('scripts')
<script type="module">
  // 1) Import Swiper core và modules
  import Swiper, { Navigation, Pagination } from 'swiper';
  // 2) Import CSS của Swiper (nếu bạn chưa import ở đâu khác)
  import 'swiper/css';
  import 'swiper/css/navigation';
  import 'swiper/css/pagination';

  // 3) Đợi DOM ready (không bắt buộc nếu bạn chắc script load sau HTML)
  document.addEventListener('DOMContentLoaded', () => {
    // Kích hoạt modules
    Swiper.use([Navigation, Pagination]);

    // 4) Collection Slider — bind Prev/Next nằm ngoài .swiper
    new Swiper('.collection-swiper', {
      slidesPerView: 2,
      spaceBetween: 16,
      loop: true,
      navigation: {
        prevEl: '.slider-full-width .swiper-button-prev',
        nextEl: '.slider-full-width .swiper-button-next',
      },
      breakpoints: {
        640: { slidesPerView: 3 },
        1024: { slidesPerView: 4 },
      },
    });

    // 5) Product Slider — bình thường với pagination
    new Swiper('.product-swiper', {
      slidesPerView: 1,
      spaceBetween: 16,
      loop: true,
      navigation: {
        prevEl: '.product-swiper .swiper-button-prev',
        nextEl: '.product-swiper .swiper-button-next',
      },
      pagination: {
        el: '.product-swiper .swiper-pagination',
        clickable: true,
      },
      breakpoints: {
        640: { slidesPerView: 2 },
        1024: { slidesPerView: 3 },
      },
    });
  });
</script>
@endpush

