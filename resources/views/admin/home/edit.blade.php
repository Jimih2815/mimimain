@extends('layouts.admin')

@section('content')
<div class="container-fluid">
  <h1 class="mb-4">Quản lý Home Page</h1>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <form action="{{ route('admin.home.update') }}"
        method="POST"
        enctype="multipart/form-data">
    @csrf

    {{-- A) Phần trước banner --}}
    <h5 class="mt-4">Phần trước Banner</h5>
    <div class="mb-3">
      <label class="form-label">Tiêu đề (H3)</label>
      <input type="text" name="pre_banner_title"
             value="{{ old('pre_banner_title', $home->pre_banner_title) }}"
             class="form-control">
      @error('pre_banner_title')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">Nội dung nút trước Banner</label>
      <input type="text" name="pre_banner_button_text"
             value="{{ old('pre_banner_button_text', $home->pre_banner_button_text) }}"
             class="form-control">
      @error('pre_banner_button_text')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">Chọn Collection cho nút trước Banner</label>
      <select name="pre_banner_button_collection_id" class="form-select">
        <option value="">-- Không chọn --</option>
        @foreach($collections as $id=>$name)
          <option value="{{ $id }}"
            @selected(old('pre_banner_button_collection_id',$home->pre_banner_button_collection_id)==$id)>
            {{ $name }}
          </option>
        @endforeach
      </select>
      @error('pre_banner_button_collection_id')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <hr>
    <h5 class="mt-4">Phần Bộ Sưu Tập</h5>

    <div class="mb-3">
      <label class="form-label">Tiêu đề bộ sưu tập (H2)</label>
      <input type="text" name="collection_section_title"
            value="{{ old('collection_section_title', $home->collection_section_title) }}"
            class="form-control">
      @error('collection_section_title')
        <div class="text-danger">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-3">
      <label class="form-label">Nội dung nút bộ sưu tập</label>
      <input type="text" name="collection_section_button_text"
            value="{{ old('collection_section_button_text', $home->collection_section_button_text) }}"
            class="form-control">
      @error('collection_section_button_text')
        <div class="text-danger">{{ $message }}</div>
      @enderror
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
      @error('collection_section_button_collection_id')
        <div class="text-danger">{{ $message }}</div>
      @enderror
    </div>
    <hr>

    {{-- B) Banner chính và button trung tâm --}}
    <div class="mb-3">
      <label class="form-label">Ảnh Banner chính (100vw×80vh)</label>
      <input type="file" name="banner_image" class="form-control" accept="image/*">
      @error('banner_image')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    @if($home->banner_image)
      <div class="mb-4">
        <img src="{{ asset('storage/'.$home->banner_image) }}"
             alt="Banner" class="img-fluid w-100" style="height:80vh; object-fit:cover;">
      </div>
    @endif


    <hr>
    <h5 class="mt-4">Phần Khởi Đầu (lead + nút “Xem chi tiết”)</h5>

    <div class="mb-3">
      <label class="form-label">Nội dung lead (p.lead)</label>
      <input type="text" name="intro_text"
            value="{{ old('intro_text', $home->intro_text) }}"
            class="form-control">
      @error('intro_text')<div class="text-danger">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
      <label class="form-label">Nội dung nút khởi đầu</label>
      <input type="text" name="intro_button_text"
            value="{{ old('intro_button_text', $home->intro_button_text) }}"
            class="form-control">
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
      @error('intro_button_collection_id')
        <div class="text-danger">{{ $message }}</div>
      @enderror
    </div>
    <hr>



    <div class="mb-3">
      <label class="form-label">Tiêu đề Giới thiệu</label>
      <input type="text" name="about_title"
             value="{{ old('about_title',$home->about_title) }}"
             class="form-control">
      @error('about_title')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">Nội dung Giới thiệu</label>
      <textarea name="about_text" class="form-control" rows="4">{{ old('about_text',$home->about_text) }}</textarea>
      @error('about_text')<div class="text-danger">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3 form-check">
      <input type="checkbox" name="show_button" id="show_button"
             class="form-check-input"
             @checked(old('show_button',$home->show_button))>
      <label for="show_button" class="form-check-label">Hiển thị nút trung tâm</label>
    </div>
    <div class="mb-3">
      <label class="form-label">Chọn Collection cho nút trung tâm</label>
      <select name="button_collection_id" class="form-select">
        <option value="">-- Không chọn --</option>
        @foreach($collections as $id=>$name)
          <option value="{{ $id }}"
            @selected(old('button_collection_id',$home->button_collection_id)==$id)>
            {{ $name }}
          </option>
        @endforeach
      </select>
      @error('button_collection_id')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">Nội dung nút trung tâm</label>
      <input type="text" name="button_text"
             value="{{ old('button_text',$home->button_text) }}"
             class="form-control">
      @error('button_text')<div class="text-danger">{{ $message }}</div>@enderror
    </div>

    <button class="btn btn-primary">Lưu thay đổi</button>
  </form>
</div>
@endsection
