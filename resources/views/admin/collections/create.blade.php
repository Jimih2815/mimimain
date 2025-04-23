@extends('layouts.admin')

@section('content')
<div class="container-fluid">
  <h1 class="mb-4">Tạo Bộ sưu tập</h1>

  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.collections.store') }}" method="POST">
    @csrf

    <div class="mb-3">
      <label class="form-label">Name</label>
      <input name="name" value="{{ old('name') }}" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Slug</label>
      <input name="slug" value="{{ old('slug') }}" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Description</label>
      <textarea name="description" class="form-control">{{ old('description') }}</textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Chọn sản phẩm</label>
      <select name="products[]" class="form-select" multiple size="8">
        @foreach($products as $p)
          <option value="{{ $p->id }}" {{ in_array($p->id, old('products', [])) ? 'selected' : '' }}>
            {{ $p->name }}
          </option>
        @endforeach
      </select>
      <small class="text-muted">Giữ Ctrl/Cmd để chọn nhiều.</small>
    </div>

    <button class="btn btn-success">Lưu</button>
    <a href="{{ route('admin.collections.index') }}" class="btn btn-secondary">Hủy</a>
  </form>
</div>
@endsection
