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

    {{-- 1) Banner --}}
    <div class="mb-3">
      <label class="form-label">Ảnh Banner (100vw × 80vh)</label>
      <input type="file" name="banner_image" class="form-control" accept="image/*">
      @error('banner_image') <div class="text-danger">{{ $message }}</div> @enderror
    </div>

    @if($home->banner_image)
      <div class="mb-4">
        <img src="{{ asset('storage/'.$home->banner_image) }}"
             alt="Banner" class="img-fluid w-100" style="height:80vh; object-fit:cover;">
      </div>
    @endif

    {{-- 2) Tiêu đề & nội dung giới thiệu --}}
    <div class="mb-3">
      <label class="form-label">Tiêu đề Giới thiệu</label>
      <input type="text" name="about_title"
             value="{{ old('about_title', $home->about_title) }}"
             class="form-control">
      @error('about_title') <div class="text-danger">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
      <label class="form-label">Nội dung Giới thiệu</label>
      <textarea name="about_text" class="form-control" rows="4">{{ old('about_text', $home->about_text) }}</textarea>
      @error('about_text') <div class="text-danger">{{ $message }}</div> @enderror
    </div>

    {{-- 3) Button Control --}}
    <div class="mb-3 form-check">
      <input type="checkbox" name="show_button" id="show_button"
             class="form-check-input"
             @checked(old('show_button', $home->show_button))>
      <label for="show_button" class="form-check-label">
        Hiển thị nút trung tâm
      </label>
    </div>

    <div class="mb-3">
      <label class="form-label">Chọn Collection cho nút</label>
      <select name="button_collection_id" class="form-select">
        <option value="">-- Không chọn --</option>
        @foreach($collections as $id => $name)
          <option value="{{ $id }}"
            @selected(old('button_collection_id', $home->button_collection_id)==$id)>
            {{ $name }}
          </option>
        @endforeach
      </select>
      @error('button_collection_id') <div class="text-danger">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
      <label class="form-label">Nội dung nút trung tâm</label>
      <input type="text" name="button_text"
             value="{{ old('button_text', $home->button_text) }}"
             class="form-control">
      @error('button_text') <div class="text-danger">{{ $message }}</div> @enderror
    </div>

    <button class="btn btn-primary">Lưu thay đổi</button>
  </form>
</div>
@endsection
