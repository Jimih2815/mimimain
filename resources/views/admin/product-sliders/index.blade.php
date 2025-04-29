@extends('layouts.admin')

@section('content')
<div class="container-fluid">
  <h1 class="mb-4">Slider Sản phẩm</h1>
  
  {{-- Form chỉnh Tiêu đề Slider Sản phẩm --}}
  <form action="{{ route('admin.home.update') }}" method="POST" class="mb-4">
    @csrf
    <div class="row g-2 align-items-end">
      <div class="col">
        <label class="form-label">Tiêu đề Slider Sản phẩm</label>
        <input type="text"
               name="product_slider_title"
               class="form-control"
               value="{{ old('product_slider_title', $home->product_slider_title) }}">
        @error('product_slider_title')
          <div class="text-danger">{{ $message }}</div>
        @enderror
      </div>
      <div class="col-auto">
        <button class="btn btn-secondary">Lưu tiêu đề</button>
      </div>
    </div>
  </form>
  {{-- /Form --}}

  <a href="{{ route('admin.product-sliders.create') }}" class="btn btn-primary mb-3">
    + Thêm slider
  </a>

  <table class="table table-bordered table-hover">
    <thead><tr>
      <th>#</th>
      <th>Ảnh</th>
      <th>Sản phẩm</th>
      <th>Thứ tự</th>
      <th>Thao tác</th>
    </tr></thead>
    <tbody>
      @foreach($sliders as $s)
      <tr>
        <td>{{ $s->id }}</td>
        <td><img src="{{ asset('storage/'.$s->image) }}" width="80" alt=""></td>
        <td>{{ $s->product->name }}</td>
        <td>{{ $s->sort_order }}</td>
        <td class="d-flex gap-1">
          <a href="{{ route('admin.product-sliders.edit', $s) }}" class="btn btn-sm btn-warning">Sửa</a>
          <form action="{{ route('admin.product-sliders.destroy', $s) }}"
                method="POST" onsubmit="return confirm('Xóa?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-danger">Xóa</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
