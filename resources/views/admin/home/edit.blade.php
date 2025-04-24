@extends('layouts.admin')

@section('content')
<div class="container-fluid">
  <h1 class="mb-4">Quản lý Home Banner</h1>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <form action="{{ route('admin.home.update') }}"
        method="POST"
        enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
      <label class="form-label">Ảnh Banner (100vw × 80vh)</label>
      <input type="file"
             name="banner_image"
             class="form-control"
             accept="image/*"
             required>
    </div>

    @if($home->banner_image)
      <div class="mb-4">
        <p>Preview:</p>
        <img src="{{ asset('storage/'.$home->banner_image) }}"
             alt="Current Banner"
             style="width:100vw; height:80vh; object-fit:cover; object-position:center;">
      </div>
    @endif

    <button class="btn btn-primary">Cập nhật Banner</button>
  </form>
</div>
@endsection
