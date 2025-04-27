@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h1 class="mb-4">Thêm Mục Sidebar</h1>

  <form action="{{ route('admin.sidebar-items.store') }}" method="POST">
    @csrf

    <div class="mb-3">
      <label class="form-label">Tên mục</label>
      <input type="text" name="name" class="form-control"
             value="{{ old('name') }}" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Thuộc mục cha</label>
      <select name="parent_id" class="form-select">
        <option value="">— Không —</option>
        @foreach($parents as $id => $parentName)
          <option value="{{ $id }}"
            {{ old('parent_id', request('parent_id')) == $id ? 'selected' : '' }}>
            {{ $parentName }}
          </option>
        @endforeach
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Chỉ định Collection</label>
      <select name="collection_id" class="form-select">
        <option value="">— Chưa chọn —</option>
        @foreach($collections as $id => $colName)
          <option value="{{ $id }}" {{ old('collection_id')==$id ? 'selected' : '' }}>
            {{ $colName }}
          </option>
        @endforeach
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Thứ tự (số nhỏ lên trước)</label>
      <input type="number" name="sort_order" class="form-control"
             value="{{ old('sort_order',0) }}">
    </div>

    <button class="btn btn-primary">Tạo mới</button>
  </form>
</div>
@endsection
