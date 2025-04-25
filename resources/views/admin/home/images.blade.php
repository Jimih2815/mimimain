@extends('layouts.admin')

@php
  use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="container-fluid">
  <h1 class="mb-4">Quản lý 2 ảnh Section</h1>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <form action="{{ route('admin.home-section-images.update') }}"
        method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
      @foreach($images as $img)
        <div class="col-md-6">
          <h5>Ảnh #{{ $img->position + 1 }}</h5>

          {{-- 1) File input --}}
          <div class="mb-3">
            <label class="form-label">Tải ảnh (ratio 2.88:1)</label>
            <input type="file"
                   name="images[{{ $img->position }}][image]"
                   class="form-control"
                   accept="image/*">
          </div>

          {{-- 2) Chọn Collection --}}
          <div class="mb-3">
            <label class="form-label">Chọn Collection</label>
            <select name="images[{{ $img->position }}][collection_id]"
                    class="form-select">
              <option value="">-- Không chọn --</option>
              @foreach($collections as $id => $name)
                <option value="{{ $id }}"
                  @selected($img->collection_id == $id)>
                  {{ $name }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- 3) Preview ảnh hiện tại --}}
          <div class="mb-3">
            <label class="form-label">Ảnh hiện tại</label>

            @if($img->image && Storage::disk('public')->exists($img->image))
              <img src="{{ asset('storage/'.$img->image) }}"
                   alt="Section Image #{{ $img->position + 1 }}"
                   class="img-fluid rounded mb-2">
            @else
              <div class="text-muted">Chưa có ảnh hoặc file không tìm thấy</div>
            @endif

          </div>
        </div>
      @endforeach

      {{-- Nút submit --}}
      <div class="col-12">
        <button class="btn btn-primary">Cập nhật ảnh Section</button>
      </div>
    </div>
  </form>
</div>
@endsection
