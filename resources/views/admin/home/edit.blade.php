@extends('layouts.admin')

@section('content')
<div class="container-fluid trang-edit-home">
 

  <h1 class="mb-4 tieu-de">Quản lý Trang Chủ</h1>

 

  <form action="{{ route('admin.home.update') }}"
        method="POST"
        enctype="multipart/form-data">
    @csrf
    <button class="btn btn-luu">Lưu thay đổi</button>
    {{-- A) Phần trước Banner --}}
    <h1 class="mt-4">Phần trước Banner</h1>
    <div class="mb-3">
      <label class="form-label">Tiêu đề (H3)</label>
      <input type="text"
             name="pre_banner_title"
             value="{{ old('pre_banner_title', $home->pre_banner_title) }}"
             class="form-control full-width">
      @error('pre_banner_title')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">Nội dung nút trước Banner</label>
      <input type="text"
             name="pre_banner_button_text"
             value="{{ old('pre_banner_button_text', $home->pre_banner_button_text) }}"
             class="form-control full-width">
      @error('pre_banner_button_text')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">Chọn Collection cho nút trước Banner</label>
      <select name="pre_banner_button_collection_id" class="form-select">
        <option value="">-- Không chọn --</option>
        @foreach($collections as $id => $name)
          <option value="{{ $id }}"
            @selected(old('pre_banner_button_collection_id', $home->pre_banner_button_collection_id)==$id)>
            {{ $name }}
          </option>
        @endforeach
      </select>
      @error('pre_banner_button_collection_id')<div class="text-danger">{{ $message }}</div>@enderror
    </div>

    

    <hr>
    {{-- C) Banner chính & nút trung tâm --}}
    <h1 class="margintop6rem">Sửa Banner chính và nút trung tâm</h1>
    <div class="mt-3 mb-3">
      <label class="form-label">Ảnh Banner chính (100vw×80vh)</label>
      <input type="file"
             name="banner_image"
             class="form-control full-width"
             accept="image/*">
      @error('banner_image')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    @if($home->banner_image)
      <div class="mb-4">
        <img src="{{ asset('storage/'.$home->banner_image) }}"
             alt="Banner"
             class="img-fluid w-100"
             style="height:80vh; object-fit:cover;">
      </div>
    @endif

    {{-- hidden để luôn gửi show_button về controller --}}
    <input type="hidden" name="show_button" value="0">
    <div class="mb-3 form-check">
      <input type="checkbox"
             name="show_button"
             id="show_button"
             class="form-check-input custom-checkbox"
             value="1"
             @checked(old('show_button', $home->show_button))>
      <label for="show_button" class="form-check-label">
        Hiển thị nút trung tâm
      </label>
    </div>

    <div class="mb-3">
      <label class="form-label">Nội dung nút trung tâm</label>
      <input type="text"
             name="button_text"
             value="{{ old('button_text', $home->button_text) }}"
             class="form-control full-width">
      @error('button_text')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">Chọn Collection cho nút trung tâm</label>
      <select name="button_collection_id" class="form-select">
        <option value="">-- Không chọn --</option>
        @foreach($collections as $id => $name)
          <option value="{{ $id }}"
            @selected(old('button_collection_id', $home->button_collection_id)==$id)>
            {{ $name }}
          </option>
        @endforeach
      </select>
      @error('button_collection_id')<div class="text-danger">{{ $message }}</div>@enderror
    </div>


    



    <hr>
    {{-- D) Phần Khởi Đầu (lead + nút “Xem chi tiết”) --}}
    <h1 class="margintop6rem">Sửa phần text và link dưới Banner </h1>
    <div class="mb-3">
      <label class="form-label">Nội dung lead (p.lead)</label>
      <input type="text"
             name="intro_text"
             value="{{ old('intro_text', $home->intro_text) }}"
             class="form-control full-width">
      @error('intro_text')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">Nội dung nút khởi đầu</label>
      <input type="text"
             name="intro_button_text"
             value="{{ old('intro_button_text', $home->intro_button_text) }}"
             class="form-control full-width">
      @error('intro_button_text')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">Chọn Collection cho nút khởi đầu</label>
      <select name="intro_button_collection_id" class="form-select">
        <option value="">-- Không chọn --</option>
        @foreach($collections as $id => $name)
          <option value="{{ $id }}"
            @selected(old('intro_button_collection_id', $home->intro_button_collection_id)==$id)>
            {{ $name }}
          </option>
        @endforeach
      </select>
      @error('intro_button_collection_id')<div class="text-danger">{{ $message }}</div>@enderror
    </div>

      <!--  THÊM PREVIEW VÀ CHỈNH SỬA COLLECTION SLIDER Ở ĐÂY -->
      <hr>
      <h1 class="margintop6rem">Preview và sửa Collection Slider</h1>

    <h5 class="mt-4">Preview Collection Slider</h5>
    <div class="d-flex overflow-auto py-2 collection-slider">
      @foreach($collectionSliders as $slider)
        <div class="flex-shrink-0 me-3" style="width:200px;">
          <div class="card">
            <img src="{{ asset('storage/'.$slider->image) }}"
                 class="card-img-top" alt="">
            <div class="card-body p-2 text-center">
              <small class="text-muted">{{ $slider->text }}</small>
            </div>
          </div>
        </div>
      @endforeach
    </div>
    <a href="{{ route('admin.collection-sliders.index') }}"
       class="btn btn-slide mb-4">
      Chỉnh sửa Collection Slider
    </a>



    
    <hr>
    {{-- B) Phần Bộ Sưu Tập --}}
    <h1 class="margintop6rem">Sửa link và text Bộ Sưu Tập</h1>

    <div class="mb-3">
      <label class="form-label">Tiêu đề bộ sưu tập (H2)</label>
      <input type="text"
             name="collection_section_title"
             value="{{ old('collection_section_title', $home->collection_section_title) }}"
             class="form-control full-width">
      @error('collection_section_title')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">Nội dung nút bộ sưu tập</label>
      <input type="text"
             name="collection_section_button_text"
             value="{{ old('collection_section_button_text', $home->collection_section_button_text) }}"
             class="form-control full-width">
      @error('collection_section_button_text')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">Chọn Collection cho nút bộ sưu tập</label>
      <select name="collection_section_button_collection_id" class="form-select">
        <option value="">-- Không chọn --</option>
        @foreach($collections as $id => $name)
          <option value="{{ $id }}"
            @selected(old('collection_section_button_collection_id', $home->collection_section_button_collection_id)==$id)>
            {{ $name }}
          </option>
        @endforeach
      </select>
      @error('collection_section_button_collection_id')<div class="text-danger">{{ $message }}</div>@enderror
    </div>


    <!-- THÊM PREVIEW VÀ CHỈNH SỬA admin/home-section-images VÀO ĐÂY -->
    <hr>
    <h5 class="mt-4">Preview Home Section Images</h5>
    <div class="d-flex flex-wrap gap-3 mb-4">
      @foreach($homeSectionImages->take(2) as $sectionImage)
        <div class="card" style="width: 200px;">
          <img src="{{ asset('storage/'.$sectionImage->image) }}"
               class="card-img-top"
               alt="Home Section Image">
        </div>
      @endforeach
    </div>
    <a href="{{ url('/admin/home-section-images') }}"
       class="btn btn-slide mb-4">
      Chỉnh sửa Banner 2 ảnh
    </a>





    <!-- THÊM PREVIEW VÀ CHỈNH SỬA PRODUCT SLIDER Ở ĐÂY -->
    <hr>
    <h1 class="margintop6rem">Preview và sửa Collection Slider</h1>

    <div class="d-flex overflow-auto py-2 product-slider">
      @foreach($productSliders as $slider)
        <div class="flex-shrink-0 me-3" style="width:200px;">
          <div class="card">
            <div class="image-wrapper">  <img src="{{ asset('storage/'.$slider->image) }}"
                  class="card-img-top" alt=""> </div>
            <div class="card-body p-2 text-center">
              <small class="text-muted">{{ $slider->product->name }}</small>
            </div>
          </div>
        </div>
      @endforeach
    </div>
    <a href="{{ route('admin.product-sliders.index') }}"
       class="btn btn-slide mb-4">
      Chỉnh sửa Product Slider
    </a>




    <hr>
    {{-- E) Giới thiệu (About) --}}
    <h1 class="margintop6rem">Sửa phần Giới thiệu Shop</h1>

    <div class="mt-3">
      <label class="form-label">Tiêu đề Giới thiệu</label>
      <input type="text"
             name="about_title"
             value="{{ old('about_title', $home->about_title) }}"
             class="form-control full-width">
      @error('about_title')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">Nội dung Giới thiệu</label>
      <textarea name="about_text"
                class="form-control full-width"
                rows="4">{{ old('about_text', $home->about_text) }}</textarea>
      @error('about_text')<div class="text-danger">{{ $message }}</div>@enderror
    </div>

  </form>
</div>
@endsection
