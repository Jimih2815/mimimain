@php
  $isEdit = isset($sector);
  $action = $isEdit
    ? route('admin.sectors.update', $sector)
    : route('admin.sectors.store');
@endphp

@extends('layouts.admin')

@section('content')
  <h1>{{ $isEdit ? 'Chỉnh sửa' : 'Thêm mới' }} Ngành hàng</h1>

  <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($isEdit)
      @method('PUT')
    @endif

    <div class="mb-3">
      <label class="form-label">Tên ngành hàng</label>
      <input
        type="text"
        name="name"
        class="form-control"
        value="{{ old('name', $sector->name ?? '') }}"
        required>
    </div>

    <div class="mb-3">
      <label class="form-label">Collection</label>
      <select name="collection_id" class="form-select" required>
        <option value="">-- Chọn --</option>
        @foreach($collections as $id => $title)
          <option
            value="{{ $id }}"
            {{ old('collection_id', $sector->collection_id ?? '') == $id ? 'selected' : '' }}>
            {{ $title }}
          </option>
        @endforeach
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Ảnh đại diện</label>
      <input
        type="file"
        name="image"
        class="form-control"
        {{ $isEdit ? '' : 'required' }}>
      @if($isEdit)
        <div class="mt-2">
          <img
            src="{{ asset('storage/'.$sector->image) }}"
            width="120"
            alt="Current image">
        </div>
      @endif
    </div>

    <div class="mb-3">
      <label class="form-label">Thứ tự (sort order)</label>
      <input
        type="number"
        name="sort_order"
        class="form-control"
        value="{{ old('sort_order', $sector->sort_order ?? 0) }}">
    </div>

    <button type="submit" class="btn btn-success">
      {{ $isEdit ? 'Cập nhật' : 'Tạo mới' }}
    </button>
    <a href="{{ route('admin.sectors.index') }}" class="btn btn-secondary ms-2">Hủy</a>
  </form>
@endsection
