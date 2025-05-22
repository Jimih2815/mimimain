@php
  // Xác định xem đang tạo mới hay chỉnh sửa
  $isEdit = ! empty($sector->id);
  // Thiết lập route tương ứng
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

    {{-- Tên Sector --}}
    <div class="mb-3">
      <label for="name" class="form-label">Tên Sector</label>
      <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $sector->name) }}" required>
    </div>

    {{-- Slug --}}
    <div class="mb-3">
      <label for="slug" class="form-label">Slug</label>
      <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $sector->slug) }}" required>
    </div>

    {{-- Ảnh Sector --}}
    <div class="mb-3">
      <label class="form-label">Ảnh Sector</label>
      @if($isEdit && $sector->image)
        <div><img src="{{ asset('storage/'.$sector->image) }}" width="150"></div>
      @endif
      <input type="file" name="image" class="form-control">
    </div>

    {{-- STT Hiển thị --}}
    <div class="mb-3">
      <label for="sort_order" class="form-label">STT Hiển thị</label>
      <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', $sector->sort_order) }}">
    </div>

    {{-- Các Collection --}}
    <div class="mb-3">
      <label class="form-label">Các Collection</label>
      <table class="table" id="selected-collections-table">
        <thead>
          <tr>
            <th>Collection</th>
            <th>Tên hiển thị</th>
            <th>Ảnh hiển thị</th>
            <th>STT</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          @foreach(old('collections', $sector->collections->map(fn($c)=>[
              'id'           => $c->id,
              'name'         => $c->name,
              'custom_name'  => $c->pivot->custom_name,
              'custom_image' => $c->pivot->custom_image,
              'sort_order'   => $c->pivot->sort_order
          ])) as $index => $col)
            <tr>
              <td>
                <input type="hidden"
                      name="collections[{{ $index }}][collection_id]"
                      value="{{ $col['collection_id'] ?? $col['id'] }}">
                {{ $col['name'] }}
              </td>
              <td>
                <input type="text"
                      name="collections[{{ $index }}][custom_name]"
                      class="form-control"
                      value="{{ old('collections.'.$index.'.custom_name', $col['custom_name']) }}">
              </td>
              <td>
                @if(! empty($col['custom_image']))
                  <img src="{{ asset('storage/'.$col['custom_image']) }}" width="80"><br>
                @endif
                <input type="file"
                      name="collections[{{ $index }}][custom_image]"
                      class="form-control form-control-sm">
              </td>
              <td>
                <input type="number"
                      name="collections[{{ $index }}][sort_order]"
                      class="form-control form-control-sm"
                      value="{{ old('collections.'.$index.'.sort_order', $col['sort_order']) }}">
              </td>
              <td>
                <button type="button" class="btn btn-danger btn-sm remove-collection">Xóa</button>
              </td>
            </tr>
          @endforeach

        </tbody>
      </table>
      <button type="button" id="add-collection-btn" class="btn btn-primary">+ Collection</button>
    </div>

    <button type="submit" class="btn btn-success">{{ $isEdit ? 'Cập nhật' : 'Tạo mới' }}</button>
    <a href="{{ route('admin.sectors.index') }}" class="btn btn-secondary ms-2">Hủy</a>
  </form>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Tạo danh sách tất cả collections từ server
    const allCollections = @json($collections);
    let counter = Date.now();
    const tableBody = document.querySelector('#selected-collections-table tbody');

    // Thêm dòng mới cho collection
    document.getElementById('add-collection-btn').addEventListener('click', function() {
      counter += 1;
      const index = 'new_' + counter;
      let options = '<option value="">-- Chọn collection --</option>';
      for (const [id, name] of Object.entries(allCollections)) {
        options += `<option value="${id}">${name}</option>`;
      }
      const row = document.createElement('tr');
      row.innerHTML = `
        <td><select name="collections[${index}][collection_id]" class="form-select form-select-sm" required>${options}</select></td>
        <td><input type="text" name="collections[${index}][custom_name]" class="form-control form-control-sm"></td>
        <td><input type="file" name="collections[${index}][custom_image]" class="form-control form-control-sm"></td>
        <td><input type="number" name="collections[${index}][sort_order]" class="form-control form-control-sm" value="0"></td>
        <td><button type="button" class="btn btn-danger btn-sm remove-collection">Xóa</button></td>
      `;
      tableBody.appendChild(row);
    });

    // Xử lý xóa dòng collection
    tableBody.addEventListener('click', function(e) {
      if (e.target.classList.contains('remove-collection')) {
        e.target.closest('tr').remove();
      }
    });

    // Gợi ý: tích hợp select2 hoặc plugin khác để có dropdown tìm kiếm
  });
</script>
@endpush
