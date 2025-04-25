@extends('layouts.admin')

@section('content')
<div class="container">
  <h1 class="mb-4">Sửa Slider sản phẩm</h1>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  {{-- Hiển thị lỗi validation --}}
  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.product-sliders.update', $slider) }}"
        method="POST"
        enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- Chọn product --}}
    <div class="mb-3">
      <label class="form-label">Sản phẩm</label>
      <select name="product_id" class="form-control" required>
        @foreach($products as $p)
          <option value="{{ $p->id }}"
            @if(old('product_id', $slider->product_id)==$p->id) selected @endif>
            {{ $p->name }}
          </option>
        @endforeach
      </select>
    </div>

    {{-- Ảnh --}}
    <div class="mb-3">
      <label class="form-label">Ảnh slider (nếu để trống sẽ lấy ảnh sản phẩm gốc)</label>
      <input type="file" name="image" class="form-control" accept="image/*">
    </div>
    @if($slider->image)
      <div class="mb-3">
        <p>Ảnh hiện tại:</p>
        <img src="{{ asset('storage/'.$slider->image) }}" width="150" alt="">
      </div>
    @endif

    {{-- Thứ tự --}}
    <div class="mb-3">
      <label class="form-label">Thứ tự (sort_order)</label>
      <input name="sort_order"
             type="number"
             class="form-control"
             value="{{ old('sort_order', $slider->sort_order) }}">
    </div>

    <button class="btn btn-primary">Cập nhật</button>
    <a href="{{ route('admin.product-sliders.index') }}" class="btn btn-secondary">Hủy</a>
  </form>
</div>
@endsection
