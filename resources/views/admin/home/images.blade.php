@extends('layouts.admin')

@section('content')
<div class="container-fluid">
  <h1 class="mb-4">Quản lý 2 ảnh Section</h1>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <form action="{{ route('admin.home.images.update') }}"
        method="POST"
        enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
      @foreach($images as $img)
      <div class="col-md-6">
        <h5>Ảnh #{{ $img->position + 1 }}</h5>

        {{-- File input --}}
        <div class="mb-3">
          <label class="form-label">Tải ảnh (ratio 2.88:1)</label>
          <input type="file"
                 name="images[{{ $img->position }}][image]"
                 class="form-control"
                 accept="image/*">
        </div>

        {{-- Preview --}}
        @if($img->image)
        <div class="mb-3">
          <img src="{{ asset('storage/'.$img->image) }}"
               alt=""
               style="width:100%; aspect-ratio:2.88/1; object-fit:cover;">
        </div>
        @endif

        {{-- Chọn collection để link --}}
        <div class="mb-3">
          <label class="form-label">Chọn Collection</label>
          <select name="images[{{ $img->position }}][collection_id]"
                  class="form-select">
            <option value="">-- None --</option>
            @foreach($collections as $id => $name)
              <option value="{{ $id }}"
                {{ $img->collection_id == $id ? 'selected' : '' }}>
                {{ $name }}
              </option>
            @endforeach
          </select>
        </div>
      </div>
      @endforeach
    </div>

    <button class="btn btn-primary">Lưu thay đổi</button>
  </form>
</div>
@endsection
