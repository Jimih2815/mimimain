@extends('layouts.admin')

@section('content')
<div class="container-fluid">
  <h1 class="mb-4">{{ $placement->exists ? 'Sửa' : 'Tạo' }} Placement</h1>

  @if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">
      @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul></div>
  @endif

  <form action="{{ $action }}" method="POST">
    @csrf
    @if($placement->exists) @method('PUT') @endif

    <div class="mb-3">
      <label class="form-label">Region</label>
      <input name="region"
             value="{{ old('region', $placement->region) }}"
             class="form-control"
             placeholder="ví dụ home_explore_button"
             required>
      <small class="text-muted">Identifier duy nhất cho vùng hiển thị.</small>
    </div>

    <div class="mb-3">
      <label class="form-label">Widget</label>
      <select name="widget_id" class="form-select" required>
        <option value="">— Chọn Widget —</option>
        @foreach($widgets as $w)
          <option value="{{ $w->id }}"
            {{ old('widget_id', $placement->widget_id)==$w->id ? 'selected':'' }}>
            {{ $w->slug }} ({{ $w->name }})
          </option>
        @endforeach
      </select>
    </div>

    <button class="btn btn-success">
      {{ $placement->exists ? 'Cập nhật' : 'Tạo mới' }}
    </button>
    <a href="{{ route('admin.placements.index') }}" class="btn btn-secondary">Hủy</a>
  </form>
</div>
@endsection
