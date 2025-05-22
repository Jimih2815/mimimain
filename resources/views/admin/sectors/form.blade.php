@php
  // Chỉ coi là "edit" khi model đã có id
  $isEdit = ! empty($sector->id);

  // Khi update phải truyền id chứ không phải object rỗng
  $action = $isEdit
    ? route('admin.sectors.update', $sector->id)
    : route('admin.sectors.store');
@endphp


@extends('layouts.admin')

@section('content')
  <h1>{{ $isEdit ? 'Chỉnh sửa' : 'Tạo mới' }} Sector</h1>

  <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($isEdit) @method('PUT') @endif

    {{-- Name --}}
    <div class="mb-3">
      <label class="form-label">Tên</label>
      <input
        type="text"
        name="name"
        class="form-control"
        value="{{ old('name', $sector->name ?? '') }}"
        required>
    </div>

    {{-- Slug --}}
    <div class="mb-3">
      <label class="form-label">Slug (URL)</label>
      <input
        type="text"
        name="slug"
        class="form-control"
        value="{{ old('slug', $sector->slug ?? '') }}"
        placeholder="ví dụ: den-ngu"
        required>
    </div>

    {{-- Image của sector --}}
    <div class="mb-3">
      <label class="form-label">Ảnh đại diện</label>
      @if($isEdit)
        <div><img src="{{ asset('storage/'.$sector->image) }}" width="120"></div>
      @endif
      <input type="file" name="image" class="form-control" {{ $isEdit ? '' : 'required' }}>
    </div>

    {{-- Sort order --}}
    <div class="mb-3">
      <label class="form-label">Thứ tự</label>
      <input type="number" name="sort_order" class="form-control"
        value="{{ old('sort_order', $sector->sort_order ?? 0) }}">
    </div>

    {{-- Gắn nhiều collections --}}
    <div class="mb-3">
      <label class="form-label">Các Collection</label>
      <table class="table">
        <thead>
          <tr>
            <th>Chọn</th>
            <th>Collection</th>
            <th>Tên hiển thị</th>
            <th>Ảnh hiển thị</th>
            <th>STT</th>
          </tr>
        </thead>
        <tbody>
          @foreach($collections as $colId => $title)
            @php
              $pivot = $sector->collections->firstWhere('pivot.collection_id',$colId)->pivot ?? null;
            @endphp
            <tr>
              <td>
                <input type="checkbox"
                  name="collections[{{ $colId }}][active]"
                  {{ old("collections.{$colId}.active", $pivot?->collection_id ? true : false) ? 'checked' : '' }}>
              </td>
              <td>{{ $title }}</td>
              <td>
                <input type="text"
                  name="collections[{{ $colId }}][custom_name]"
                  class="form-control"
                  value="{{ old("collections.{$colId}.custom_name", $pivot->custom_name ?? '') }}">
              </td>
              <td>
                @if($pivot && $pivot->custom_image)
                  <img src="{{ asset('storage/'.$pivot->custom_image) }}" width="80"><br>
                @endif
                <input type="file"
                  name="collections[{{ $colId }}][custom_image]"
                  class="form-control form-control-sm">
              </td>
              <td>
                <input type="number"
                  name="collections[{{ $colId }}][sort_order]"
                  class="form-control form-control-sm"
                  value="{{ old("collections.{$colId}.sort_order", $pivot->sort_order ?? 0) }}">
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <button type="submit" class="btn btn-success">
      {{ $isEdit ? 'Cập nhật' : 'Tạo mới' }}
    </button>
    <a href="{{ route('admin.sectors.index') }}" class="btn btn-secondary ms-2">Hủy</a>
  </form>
@endsection
