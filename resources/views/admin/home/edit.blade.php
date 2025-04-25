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
      <input type="file"
             name="banner_image"
             class="form-control"
             accept="image/*">
      @error('banner_image')
        <div class="text-danger">{{ $message }}</div>
      @enderror
    </div>

    @if($home->banner_image)
      <div class="mb-4">
        <p>Preview hiện tại:</p>
        <img src="{{ asset('storage/'.$home->banner_image) }}"
             alt="Current Banner"
             style="width:100vw; height:80vh; object-fit:cover; object-position:center;">
      </div>
    @endif




    {{-- 2) Tiêu đề giới thiệu --}}
    <h1 style="margin-top:15rem;     text-align: center;"> CHỈNH SỬA PHẦN GIỚI THIỆU Ở CUỐI TRANG</h1>
    
    <div class="mb-3">
      <label class="form-label">Tiêu đề Giới thiệu</label>
      <input type="text"
             name="about_title"
             value="{{ old('about_title', $home->about_title) }}"
             class="form-control">
      @error('about_title')
        <div class="text-danger">{{ $message }}</div>
      @enderror
    </div>

    {{-- 3) Nội dung giới thiệu --}}
    <div class="mb-3">
      <label class="form-label">Nội dung Giới thiệu</label>
      <textarea name="about_text"
                class="form-control"
                rows="4">{{ old('about_text', $home->about_text) }}</textarea>
      @error('about_text')
        <div class="text-danger">{{ $message }}</div>
      @enderror
    </div>

    <button class="btn btn-primary">Lưu thay đổi</button>
  </form>
</div>
@endsection
