@extends('layouts.app')

@section('title','Trang Chủ')

@section('content')
  {{-- Banner động --}}
  <div class="position-relative" style="width:100%; height:100vh; overflow:hidden;">
    <img src="{{ asset('storage/'.$home->banner_image) }}"
         alt="Home Banner"
         style="width:100vw; height:100vh; object-fit:cover; object-position:center;">
    <div class="position-absolute top-50 start-50 translate-middle">
    @php
      $nut = widget_for('nut-banner');
    @endphp

    @if($nut && $nut->collection)
      @if($nut->type === 'button')
        <a href="{{ route('collections.show', $nut->collection->slug) }}"
          class="btn btn-lg btn-primary nut-banner">
          {{ $nut->button_text }}
        </a>
      @elseif($nut->type === 'banner')
        <a href="{{ route('collections.show', $nut->collection->slug) }}"
          class="nut-banner">
          <img src="{{ asset($nut->image) }}"
              alt="{{ $nut->name }}"
              style="width:100%; height:100%; object-fit:cover;">
        </a>
      @else
        {!! $nut->html !!}
      @endif
    @else
      {{-- Fallback --}}
      <a href="#" class="btn btn-lg btn-primary nut-banner">
        XEM NGAY
      </a>
    @endif
    </div>
  </div>

  {{-- Nội dung cũ của bạn, bọc trong container --}}
  <div class="container py-5">
    {{-- 1) Đoạn text --}}
    <p class="lead text-center mb-2">“Khám phá thế giới quà tặng đầy màu sắc!”</p>

    {{-- 2) Link --}}
    <div class="text-center mb-5">
      <a href="#" class="text-decoration-none fw-semibold">Xem chi tiết →</a>
    </div>

    {{-- 4) Hai ảnh chữ nhật cạnh nhau --}}
    <div class="row g-0 mb-5">
      <div class="col-6 pe-1">
        <img src="https://hoanghamobile.com/.../anh-mau-xanh-duong-42.jpg"
             class="img-fluid w-100 " alt="">
      </div>
      <div class="col-6 ps-1">
        <img src="https://hoanghamobile.com/.../anh-mau-xanh-duong-42.jpg"
             class="img-fluid w-100 " alt="">
      </div>
    </div>

    {{-- 6) Collection Slider --}}
    <div class="slider-full-width mb-5">
      <div class="swiper collection-swiper">
        <div class="swiper-wrapper">
          @foreach($sliders as $s)
            <div class="swiper-slide">
              <div class="relative w-[500px] aspect-[3/4] overflow-hidden">
                <a href="{{ route('collections.show', $s->collection->slug) }}">
                  <img src="{{ asset('storage/'.$s->image) }}"
                      alt="{{ $s->text }}"
                      class="absolute inset-0 w-full h-full object-cover object-center">
                </a>
              </div>
              <p class="mt-2 text-center font-semibold">{{ $s->text }}</p>
            </div>
          @endforeach
        </div>

        {{-- nút điều hướng --}}
        <div class="swiper-button-prev text-blue-600"></div>
        <div class="swiper-button-next text-blue-600"></div>
        <div class="swiper-pagination"></div>
      </div>
    </div>

    {{-- 5) H2 + nút Shop Now (dynamic widget) --}}
    <div class="text-center mb-5">
      <h2 class="mb-3">Khám phá bộ sưu tập của chúng tôi</h2>

      @php
        // Lấy widget đã đặt vào region 'nut-bosuutap'
        $btn = widget_for('nut-bosuutap');
      @endphp

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
        {{-- fallback nếu chưa gán widget --}}
        <a href="#" class="btn btn-lg btn-primary nut-bosuutap">
          Shop Now
        </a>
      @endif
    </div>

    {{-- 7a) Hai ảnh chữ nhật xếp cạnh nhau --}}
    <div class="section-images mb-5 d-flex gap-3">
      @foreach($sectionImages as $img)
        <div class="section-images__item flex-fill">
          <a href="{{ $img->collection
                        ? route('collections.show', $img->collection->slug)
                        : '#' }}">
            <img src="{{ asset('storage/'.$img->image) }}"
                alt="Section Image {{ $loop->iteration }}"
                class="section-images__img img-fluid rounded">
          </a>
        </div>
      @endforeach
    </div>

    {{-- 7) Slide sản phẩm (hình vuông) --}}
    <div class="mb-5">
      <div class="swiper product-swiper overflow-visible">
        <div class="swiper-wrapper">
          @foreach($productSliders as $ps)
            <div class="swiper-slide flex-none w-[400px]">
              <a href="{{ route('products.show', $ps->product->slug) }}">
                <div class="aspect-square overflow-hidden">
                  <img src="{{ asset('storage/'.$ps->image) }}"
                      alt="{{ $ps->product->name }}"
                      class="w-full h-full object-cover object-center">
                </div>
              </a>
              <p class="mt-2 text-center font-semibold">
                {{ $ps->product->name }}
              </p>
              <p class="text-center">
                {{ number_format($ps->product->base_price,0,',','.') }}₫
              </p>
            </div>
          @endforeach
        </div>

        {{-- navigation --}}
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
        <div class="swiper-pagination"></div>
      </div>
    </div>

    {{-- 8) Giới thiệu về cửa hàng, lấy từ DB và text-center --}}
    <div class="container py-5 text-center">
      <h2 class="text-center mb-4">
        {{ $home->about_title }}
      </h2>
      <p class="mx-auto about-text" style="max-width:720px;">
        {{ $home->about_text }}
      </p>
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
    breakpoints: {
      640: { slidesPerView: 3 },
      1024:{ slidesPerView: 4 }
    }
  });
</script>
@endpush