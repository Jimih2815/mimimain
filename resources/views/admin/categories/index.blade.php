{{-- resources/views/admin/categories/index.blade.php --}}
@extends('layouts.app')
@section('title','Admin — Categories')

@section('content')
<div class="container py-4">
  <h2 class="mb-4">Quản lý Categories</h2>

  {{-- Thêm mới --}}
  <form action="{{ route('admin.categories.store') }}" method="POST"
        class="d-flex gap-2 mb-4">
      @csrf
      <input name="name" class="form-control w-25" placeholder="Tên category mới">
      <button class="btn btn-primary">Thêm</button>
  </form>

  <table class="table table-bordered w-50">
      <thead class="table-light">
        <tr><th>#</th><th>Tên</th><th>Thứ tự</th><th width="80"></th></tr>
      </thead>
      <tbody>
        @foreach($cats as $c)
        <tr>
          <td>{{ $c->id }}</td>
          <td>
            <form action="{{ route('admin.categories.update',$c) }}"
                  method="POST" class="d-flex gap-2">
                @csrf @method('PUT')
                <input name="name"  value="{{ $c->name }}"  class="form-control">
                <input name="sort_order" type="number" value="{{ $c->sort_order }}"
                       class="form-control" style="max-width:90px">
                <button class="btn btn-success btn-sm">Lưu</button>
            </form>
          </td>
          <td>{{ $c->sort_order }}</td>
          <td>
            <form action="{{ route('admin.categories.destroy',$c) }}"
                  method="POST"
                  onsubmit="return confirm('Xoá category này?')">
              @csrf @method('DELETE')
              <button class="btn btn-danger btn-sm">&times;</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
  </table>
</div>
@endsection
