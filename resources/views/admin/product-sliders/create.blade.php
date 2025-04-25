@extends('layouts.admin')

@section('content')
<div class="container">
  <h1 class="mb-4">Thêm Slider Sản Phẩm</h1>

  <form action="{{ route('admin.product-sliders.store') }}"
        method="POST"
        enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
      <label class="form-label">Chọn Sản Phẩm</label>
      <select name="product_id" class="form-control" required>
        <option value="">-- chọn sản phẩm --</option>
        @foreach($products as $p)
          <option value="{{ $p->id }}"
            {{ old('product_id') == $p->id ? 'selected':'' }}>
            {{ $p->name }}
          </option>
        @endforeach
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Ảnh Slider (nếu bạn muốn override ảnh mặc định của sản phẩm)</label>
      <input type="file"
             name="image"
             class="form-control"
             accept="image/*">
      <small class="form-text text-muted">
        Nếu không upload, hệ thống sẽ dùng `img` đang lưu trong bảng `products`.
      </small>
    </div>

    <div class="mb-3">
      <label class="form-label">Thứ tự (sort order)</label>
      <input type="number"
             name="sort_order"
             class="form-control"
             value="{{ old('sort_order', 0) }}"
             min="0">
    </div>

    <button class="btn btn-primary">Lưu</button>
    <a href="{{ route('admin.product-sliders.index') }}"
       class="btn btn-secondary">Hủy</a>
  </form>
</div>
@endsection
