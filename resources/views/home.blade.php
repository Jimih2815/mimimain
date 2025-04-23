@extends('layouts.app')

@section('title','Trang Chủ')

@section('content')
  <div class="container py-5">

    {{-- 1) Đoạn text --}}
    <p class="lead text-center mb-2">“Khám phá thế giới quà tặng đầy màu sắc!”</p>

    {{-- 2) Link --}}
    <div class="text-center mb-5">
      <a href="#" class="text-decoration-none fw-semibold">Xem chi tiết →</a>
    </div>

    {{-- 3) Ảnh full‑width --}}
    <div class="mb-5">
      <img src="https://hoanghamobile.com/tin-tuc/wp-content/uploads/2024/05/anh-mau-xanh-duong-42.jpg"
           alt="Banner"
           class="img-fluid w-100 rounded">
    </div>

    {{-- 4) Hai ảnh chữ nhật cạnh nhau --}}
    <div class="row g-0 mb-5">
      <div class="col-6 pe-1">
        <img src="https://hoanghamobile.com/tin-tuc/wp-content/uploads/2024/05/anh-mau-xanh-duong-42.jpg"
             alt="Rect1"
             class="img-fluid w-100 rounded">
      </div>
      <div class="col-6 ps-1">
        <img src="https://hoanghamobile.com/tin-tuc/wp-content/uploads/2024/05/anh-mau-xanh-duong-42.jpg"
             alt="Rect2"
             class="img-fluid w-100 rounded">
      </div>
    </div>

    {{-- 5) H2 + nút Show Now (dynamic widget) --}}
    <div class="text-center mb-5">
      <h2 class="mb-3">Khám phá bộ sưu tập của chúng tôi</h2>

      @php
        // Lấy widget cho region 'show-now-button'
        $w = widget_for('show-now-button');
      @endphp

      @if($w && $w->collection)
        @if($w->type==='button')
          <a href="{{ route('collections.show',$w->collection->slug) }}"
            class="btn btn-lg btn-primary">
            {{ $w->button_text }}
          </a>
        @elseif($w->type==='banner')
          <a href="{{ route('collections.show',$w->collection->slug) }}">
            <img src="{{ asset($w->image) }}"
                alt="{{ $w->name }}"
                class="img-fluid rounded">
          </a>
        @else
          {!! $w->html !!}
        @endif
      @else
        {{-- Fallback nếu admin chưa gán widget --}}
        <button class="btn btn-primary">Show Now</button>
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
            <img 
            src="https://hoanghamobile.com/tin-tuc/wp-content/uploads/2024/05/anh-mau-xanh-duong-42.jpg" 
            alt="Ảnh cạnh 1" 
            class="img-fluid w-100 rounded">
        </div>
        <div class="col-6">
            <img 
            src="https://hoanghamobile.com/tin-tuc/wp-content/uploads/2024/05/anh-mau-xanh-duong-42.jpg" 
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
                <img 
                src="https://hoanghamobile.com/tin-tuc/wp-content/uploads/2024/05/anh-mau-xanh-duong-42.jpg" 
                alt="Prod {{ $i }}" 
                class="product-img rounded mx-auto d-block">
            </div>
            @endforeach
            </div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-pagination"></div>
        </div>
        {{-- 8) Giới thiệu về cửa hàng --}}
        <div class="container py-5">
            <h2 class="text-center mb-4">Về MimiMain – Nơi Quà Tặng Thăng Hoa</h2>
            <p class="text-justify">
                MimiMain không phải là một cửa hàng bình thường – chúng tôi là “bà mai” ghép đôi bạn với gấu bông ấm áp, “thợ ánh sáng” thổi hồn cho đèn ngủ lung linh, và “nghệ nhân” trang trí những góc quà tặng thật đặc biệt.  
                Từ những chú gấu bông siêu to khổng lồ đến bộ đèn hoa handmade tỏa sắc huyền ảo, mỗi sản phẩm của chúng tôi đều được tuyển chọn tỉ mỉ và kiểm định chất lượng “chuẩn chỉnh”.  
                Nếu bạn đang tìm một món quà để khơi gợi nụ cười, thắp lên kỷ niệm hay chỉ đơn giản là làm mới không gian sống, MimiMain sẽ là người bạn đồng hành không thể thiếu.  
                Chúng tôi tin rằng chỉ cần một chiếc đèn ngủ ấm áp hay một chú gấu bông ôm ấp, là tâm hồn bạn đã đủ đầy – và quà tặng đẹp cũng như cách bạn trao gửi, luôn có “điểm nhấn” riêng tại MimiMain.  
                Hãy để MimiMain biến những khoảnh khắc tặng quà trở thành kỷ niệm đáng nhớ nhất!
            </p>
            </div>

        </div>

  </div>
@endsection
