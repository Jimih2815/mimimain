@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1>Ngành hàng</h1>
  <a href="{{ route('admin.sectors.create') }}" class="btn btn-primary">Thêm mới</a>
</div>

<table class="table table-bordered">
  <thead>
    <tr>
      <th>#</th>
      <th>Ảnh</th>
      <th>Tên</th>
      <th>Collection</th>
      <th>Thứ tự</th>
      <th>Hành động</th>
    </tr>
  </thead>
  <tbody>
    @foreach($sectors as $s)
    <tr>
      <td>{{ $s->id }}</td>
      <td><img src="{{ asset('storage/'.$s->image) }}" width="60"></td>
      <td>{{ $s->name }}</td>
      <td>
  @if($s->collections->isEmpty())
    <em>Chưa có collection</em>
  @else
    @foreach($s->collections as $col)
      <span class="badge bg-secondary">
        {{ $col->pivot->custom_name ?? $col->name }}
      </span>
    @endforeach
  @endif
</td>

      <td>{{ $s->sort_order }}</td>
      <td>
        <a href="{{ route('admin.sectors.edit',$s) }}" class="btn btn-sm btn-warning">Sửa</a>
        <form action="{{ route('admin.sectors.destroy', $s) }}" method="POST" class="d-inline">
          @csrf @method('DELETE')
          <button onclick="return confirm('Xác nhận xoá?')" class="btn btn-sm btn-danger">Xóa</button>
        </form>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
@endsection
