@extends('layouts.admin')

@section('content')
<div class="container">
  <h1 class="mb-4">
    {{ $slider->exists ? 'Sửa' : 'Tạo' }} slider sản phẩm
  </h1>

  <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($slider->exists) @method('PUT') @endif

    <div class="mb-3">
      <label class="form-label">Sản phẩm</label>
      <select name="product_id" class="form-control" required>
        <option value="">-- Chọn sản phẩm --</option>
        @foreach($products as $p)
        <option value="{{ $p->id }}"
          {{ old('product_id', $slider->product_id)==$p->id ? 'selected':'' }}>
          {{ $p->name }}
        </option>
        @endforeach
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Ảnh</label>
      <input type="file" name="image" class="form-control" {{ $slider->exists?'':'required' }}>
      @if($slider->exists)
        <img src="{{ asset('storage/'.$slider->image) }}" width="150" class="mt-2" alt="">
      @endif
    </div>

    <div class="mb-3">
      <label class="form-label">Thứ tự (sort order)</label>
      <input type="number" name="sort_order"
             class="form-control"
             value="{{ old('sort_order',$slider->sort_order) }}">
    </div>

    <button class="btn btn-primary">
      {{ $slider->exists ? 'Cập nhật' : 'Lưu' }}
    </button>
  </form>
</div>
@endsection
