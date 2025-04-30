{{-- resources/views/admin/collection-sliders/index.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="trang-slider-san-pham">
  <h1 class="mb-4" style="color: #b83232; font-size: 3rem;">Collection Slider</h1>

  {{-- Form chỉnh Tiêu đề Slider Bộ sưu tập --}}
  <form action="{{ route('admin.home.update') }}" method="POST" class="mb-4">
    @csrf
    <div class="row g-2 align-items-end">
      <div class="col">
        <label class="form-label">Tiêu đề Slider Bộ sưu tập</label>
        <input type="text"
               name="collection_slider_title"
               class="form-control"
               value="{{ old('collection_slider_title', $home->collection_slider_title) }}">
        @error('collection_slider_title')
          <div class="text-danger">{{ $message }}</div>
        @enderror
      </div>
      <div class="col-auto">
        <button class="btn-mimi nut-luu">Lưu tiêu đề</button>
      </div>
    </div>
  </form>
  {{-- /Form --}}

  <a href="{{ route('admin.collection-sliders.create') }}"
     class="btn-mimi nut-them-slide mb-3">+ Thêm Bộ Sưu Tập</a>
  <p class="mt-4 mb-1 ms-2" style="font-style: italic;">Kéo/Thả chuột để thay đổi thứ tự</p>
  <table id="collection-sliders-table"
         class="table table-striped"
         style="position: relative;">
    <thead>
      <tr>
        <th style="width: 5rem;">Thứ tự</th>
        <th>Ảnh</th>
        <th>Text</th>
        <th>Collection</th>
        <th style="width: 10rem;">Action</th>
      </tr>
    </thead>
    <tbody>
      @foreach($items as $it)
      <tr data-id="{{ $it->id }}">
        {{-- handle kéo --}}
        <td class="sort-handle" style="cursor: move;">
          {{ $it->sort_order }}
        </td>
        <td>
          <img src="{{ asset('storage/'.$it->image) }}"
               width="150" class="rounded" alt="">
        </td>
        <td>{{ $it->text }}</td>
        <td>{{ $it->collection->name }}</td>
        <td class="d-flex gap-1" >
          <a href="{{ route('admin.collection-sliders.edit',$it) }}"
             class="btn-mimi nut-sua">Sửa</a>
          <form action="{{ route('admin.collection-sliders.destroy',$it) }}"
                method="POST"
                onsubmit="return confirm('Xóa hả?');">
            @csrf @method('DELETE')
            <button class="btn-mimi nut-xoa">Xóa</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection

@push('styles')
  <style>
    .trang-slider-san-pham .table-striped th {
      background-color: #4ab3af;
      border: 2px solid #fff;
      color: #fff;
      text-align: center;
      vertical-align: middle;
    }
    .trang-slider-san-pham .table-striped td {
      text-align: center;
      vertical-align: middle;
    }
    .trang-slider-san-pham .table-striped tbody tr td.d-flex {
      display: table-cell !important;
      vertical-align: middle;
      text-align: center;
      padding: 0.5rem;
    }
    .trang-slider-san-pham .table-striped tbody tr td.d-flex > * {
      display: inline-block;
      vertical-align: middle;
      margin: 0 .25rem;
    }
  </style>
@endpush

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const tbody = document.querySelector('#collection-sliders-table tbody');
      const reorderUrl = @json(route('admin.collection-sliders.reorder'));

      Sortable.create(tbody, {
        // handle: '.sort-handle',
        animation: 150,
        onEnd: function() {
          const ids = Array.from(tbody.querySelectorAll('tr'))
            .map(tr => tr.dataset.id);

          fetch(reorderUrl, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ids })
          })
          .then(r => r.json())
          .then(json => {
            if (json.status === 'success') {
              tbody.querySelectorAll('tr').forEach((tr, idx) => {
                tr.querySelector('.sort-handle').textContent = idx + 1;
              });
            } else {
              alert('Lỗi lưu thứ tự');
            }
          });
        }
      });
    });
  </script>
@endpush
