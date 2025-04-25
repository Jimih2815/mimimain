@extends('layouts.app')

@section('title','Trang Chủ')

@section('content')
  {{-- Banner động --}}
  <div class="position-relative" style="width:100vw; height:100vh; overflow:hidden;">
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
             class="img-fluid w-100 rounded" alt="">
      </div>
      <div class="col-6 ps-1">
        <img src="https://hoanghamobile.com/.../anh-mau-xanh-duong-42.jpg"
             class="img-fluid w-100 rounded" alt="">
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

    {{-- 6) Slide bộ sưu tập (3×4) --}}
    <div class="mb-5">
      <div class="swiper collection-swiper">
        <div class="swiper-wrapper">
          @foreach(range(1,6) as $i)
          <div class="swiper-slide text-center">
            <img src="https://hoanghamobile.com/tin-tuc/wp-content/uploads/2024/05/anh-mau-xanh-duong-42.jpg"
                 alt="Coll {{ $i }}"
                 class="img-fluid mb-2 rounded">
            <p>Bộ sưu tập {{ $i }}</p>
          </div>
          @endforeach
        </div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
        <div class="swiper-pagination"></div>
      </div>
    </div>

    {{-- 7a) Hai ảnh chữ nhật xếp cạnh nhau --}}
    <div class="row g-2 mb-5">
      <div class="col-6">
        <img src="https://hoanghamobile.com/tin-tuc/wp-content/uploads/2024/05/anh-mau-xanh-duong-42.jpg"
             alt="Ảnh cạnh 1"
             class="img-fluid w-100 rounded">
      </div>
      <div class="col-6">
        <img src="https://hoanghamobile.com/tin-tuc/wp-content/uploads/2024/05/anh-mau-xanh-duong-42.jpg"
             alt="Ảnh cạnh 2"
             class="img-fluid w-100 rounded">
      </div>
    </div>

    {{-- 7) Slide sản phẩm (hình vuông) --}}
    <div class="mb-5">
      <div class="swiper product-swiper">
        <div class="swiper-wrapper">
          @foreach(range(1,8) as $i)
          <div class="swiper-slide text-center">
            <img src="https://hoanghamobile.com/tin-tuc/wp-content/uploads/2024/05/anh-mau-xanh-duong-42.jpg"
                 alt="Prod {{ $i }}"
                 class="product-img rounded mx-auto d-block">
          </div>
          @endforeach
        </div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
        <div class="swiper-pagination"></div>
      </div>
    </div>

    {{-- 8) Giới thiệu về cửa hàng --}}
    <div class="container py-5">
      <h2 class="text-center mb-4">Về MimiMain – Nơi Quà Tặng Thăng Hoa</h2>
      <p class="text-justify">
        MimiMain không phải là một cửa hàng bình thường – chúng tôi là “bà mai” ghép đôi bạn với gấu bông ấm áp, “thợ ánh sáng” thổi hồn cho đèn ngủ lung linh...
      </p>
    </div>

  </div>
@endsection
