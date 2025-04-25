@extends('layouts.admin')

@section('content')
<div class="container-fluid">
  <h1 class="mb-4">Slider Sản phẩm</h1>
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
