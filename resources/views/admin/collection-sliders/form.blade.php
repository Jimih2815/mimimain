@extends('layouts.admin')

@section('content')
<div class="trang-slider-san-pham">

  <a href="{{ route('admin.collection-sliders.index') }}" class="nut-quay-ve mb-3">
    <i class="fa-solid fa-chevron-left"></i> Quay về danh sách slider
  </a>

  <h1 class="mb-4" style="color: #b83232; font-size: 3rem;">
    {{ isset($item) ? "Sửa" : "Thêm" }} Collection Slider
  </h1>

  <form action="{{ isset($item)
                   ? route('admin.collection-sliders.update',$item)
                   : route('admin.collection-sliders.store') }}"
        method="POST"
        enctype="multipart/form-data">
    @csrf
    @if(isset($item)) @method('PUT') @endif

    <div class="mb-3">
      <label class="form-label">Collection</label>
      <select name="collection_id" class="form-select" required>
        @foreach($collections as $id => $name)
          <option value="{{ $id }}"
            {{ old('collection_id',$item->collection_id??'')==$id?'selected':'' }}>
            {{ $name }}
          </option>
        @endforeach
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Text dưới ảnh</label>
      <input name="text" class="form-control"
             value="{{ old('text',$item->text??'') }}" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Ảnh (3:4)</label>
      <input type="file" name="image" class="form-control" accept="image/*"
             {{ isset($item) ? '' : 'required' }}>
    </div>

    @if(isset($item))
      <div class="mb-3">
        <img src="{{ asset('storage/'.$item->image) }}"
             class="rounded" width="120"
             style="object-fit:cover;aspect-ratio:3/4">
      </div>
    @endif

    <button class="btn-mimi nut-them-slide">
      {{ isset($item) ? 'Cập nhật' : 'Tạo mới' }}
    </button>
  </form>
</div>
@endsection
