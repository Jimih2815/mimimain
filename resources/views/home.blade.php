{{-- resources/views/home.blade.php --}}
@extends('layouts.app')

@section('title','Trang Chủ')

@section('content')
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
  <div class="container py-5">
    {{-- 2.1) Đoạn text khởi đầu --}}
    <p class="lead text-center mb-2">“Khám phá thế giới quà tặng đầy màu sắc!”</p>
    {{-- 2.2) Link detail --}}
    <div class="text-center mb-5">
      <a href="#" class="text-decoration-none fw-semibold">Xem chi tiết →</a>
    </div>

    {{-- 3) Hai ảnh chữ nhật cạnh nhau --}}
    <div class="row g-0 mb-5">
      <div class="col-6 pe-1">
        <img src="https://hoanghamobile.com/.../anh-mau-xanh-duong-42.jpg"
             class="img-fluid w-100" alt="">
      </div>
      <div class="col-6 ps-1">
        <img src="https://hoanghamobile.com/.../anh-mau-xanh-duong-42.jpg"
             class="img-fluid w-100" alt="">
      </div>
    </div>

    {{-- 4) Collection Slider --}}
    <div class="slider-full-width mb-5">
      <div class="swiper collection-swiper">
        <div class="swiper-wrapper">
          @foreach($sliders as $s)
            <div class="swiper-slide">
              <div class="position-relative overflow-hidden" style="width:500px; aspect-ratio:3/4">
                <a href="{{ route('collections.show', $s->collection->slug) }}">
                  <img src="{{ asset('storage/'.$s->image) }}"
                       alt="{{ $s->text }}"
                       class="w-100 h-100 object-fit-cover object-position-center">
                </a>
              </div>
              <p class="mt-2 text-center fw-semibold">{{ $s->text }}</p>
            </div>
          @endforeach
        </div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
        <div class="swiper-pagination"></div>
      </div>
    </div>

    {{-- 5) H2 + nút “Shop Now” (dynamic widget vùng nut-bosuutap) --}}
    <div class="text-center mb-5">
      <h2 class="mb-3">Khám phá bộ sưu tập của chúng tôi</h2>
      @php $btn = widget_for('nut-bosuutap'); @endphp

      @if($btn && $btn->type === 'button')
        <a href="{{ $btn->collection
                      ? route('collections.show', $btn->collection->slug)
                      : '#' }}"
           class="btn btn-lg btn-primary nut-bosuutap">
          {{ $btn->button_text }}
        </a>
      @elseif($btn && $btn->type === 'banner')
        <a href="{{ $btn->collection
                      ? route('collections.show', $btn->collection->slug)
                      : '#' }}"
           class="nut-bosuutap">
          <img src="{{ asset($btn->image) }}"
               alt="{{ $btn->name }}"
               class="img-fluid rounded">
        </a>
      @elseif($btn && $btn->type === 'html')
        {!! $btn->html !!}
      @else
        <a href="#" class="btn btn-lg btn-primary nut-bosuutap">
          Shop Now
        </a>
      @endif
    </div>

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

    {{-- 7) Product Slider --}}
    <div class="mb-5">
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
              <p class="mt-2 text-center fw-semibold">{{ $ps->product->name }}</p>
              <p class="text-center">
                {{ number_format($ps->product->base_price,0,',','.') }}₫
              </p>
            </div>
          @endforeach
        </div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
        <div class="swiper-pagination"></div>
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
  <script>
    new Swiper('.collection-swiper', {
      slidesPerView: 2,
      spaceBetween: 16,
      loop: true,
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
      pagination: { el: '.swiper-pagination', clickable: true },
      breakpoints: { 640: { slidesPerView: 3 }, 1024: { slidesPerView: 4 } }
    });

    new Swiper('.product-swiper', {
      slidesPerView: 1,
      spaceBetween: 16,
      loop: true,
      navigation: {
        nextEl: '.product-swiper .swiper-button-next',
        prevEl: '.product-swiper .swiper-button-prev',
      },
      pagination: { el: '.product-swiper .swiper-pagination', clickable: true },
      breakpoints: { 640: { slidesPerView: 2 }, 1024: { slidesPerView: 3 } }
    });
  </script>
@endpush
