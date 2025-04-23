{{-- resources/views/admin/widgets/form.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid">
  <h1 class="mb-4">{{ $widget->exists ? 'Sửa' : 'Tạo' }} Widget</h1>

  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ $action }}" method="POST">
    @csrf
    @if($widget->exists) @method('PUT') @endif

    {{-- Name --}}
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input name="name"
             value="{{ old('name', $widget->name) }}"
             class="form-control"
             required>
      <small class="text-muted">
        Tên hiển thị để bạn dễ quản lý (ví dụ “Banner trang chủ”)
      </small>
    </div>

    {{-- Slug --}}
    <div class="mb-3">
      <label class="form-label">Slug</label>
      <input name="slug"
             value="{{ old('slug', $widget->slug) }}"
             class="form-control"
             required>
      <small class="text-muted">
        Đường dẫn gọi widget, không chứa khoảng trắng (ví dụ “explore-gifts”)
      </small>
    </div>

    {{-- Type --}}
    <div class="mb-3">
      <label class="form-label">Type</label>
      <select name="type" class="form-select" required>
        @foreach(['banner','button','html'] as $t)
          <option value="{{ $t }}"
            {{ old('type', $widget->type) === $t ? 'selected' : '' }}>
            {{ ucfirst($t) }}
          </option>
        @endforeach
      </select>
    </div>

    {{-- Collection --}}
    <div class="mb-3">
      <label class="form-label">Collection</label>
      <select name="collection_id" class="form-select">
        <option value="">— None —</option>
        @foreach($collections as $col)
          <option value="{{ $col->id }}"
            {{ old('collection_id', $widget->collection_id) == $col->id ? 'selected' : '' }}>
            {{ $col->name }}
          </option>
        @endforeach
      </select>
    </div>

    {{-- Image URL (banner) --}}
    <div class="mb-3">
      <label class="form-label">Image URL</label>
      <input name="image"
             value="{{ old('image', $widget->image) }}"
             class="form-control">
      <small class="text-muted">
        Dùng nếu type = banner
      </small>
    </div>

    {{-- Button Text --}}
    <div class="mb-3">
      <label class="form-label">Button Text</label>
      <input name="button_text"
             value="{{ old('button_text', $widget->button_text) }}"
             class="form-control">
      <small class="text-muted">
        Dùng nếu type = button
      </small>
    </div>

    {{-- HTML --}}
    <div class="mb-3">
      <label class="form-label">HTML (nếu type = html)</label>
      <textarea name="html"
                class="form-control"
                rows="4">{{ old('html', $widget->html) }}</textarea>
    </div>

    <button class="btn btn-success">
      {{ $widget->exists ? 'Cập nhật' : 'Tạo mới' }}
    </button>
    <a href="{{ route('admin.widgets.index') }}" class="btn btn-secondary">Hủy</a>
  </form>
</div>
@endsection
