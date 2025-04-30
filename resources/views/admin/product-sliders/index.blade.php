@extends('layouts.admin')

@section('content')
<div class="trang-slider-san-pham">
  <h1 class="mb-4" style="color: #b83232; font-size: 3rem;">Slider Sản phẩm</h1>
  
  {{-- Form chỉnh Tiêu đề Slider Sản phẩm --}}
  <form action="{{ route('admin.home.update') }}" method="POST" class="mb-4">
    @csrf
    <div class="row g-2 align-items-end">
      <div class="col">
        <label class="form-label">Tiêu đề Slider Sản phẩm</label>
        <input type="text"
               name="product_slider_title"
               class="form-control"
               value="{{ old('product_slider_title', $home->product_slider_title) }}">
        @error('product_slider_title')
          <div class="text-danger">{{ $message }}</div>
        @enderror
      </div>
      <div class="col-auto">
        <button class="btn-mimi nut-luu">Lưu tiêu đề</button>
      </div>
    </div>
  </form>
  {{-- /Form --}}

  <a href="{{ route('admin.product-sliders.create') }}" class="btn-mimi nut-them-slide mb-3">
    + Thêm Sản Phẩm vào Slider
  </a>

  {{-- Thêm attribute id để JS dễ chọn --}}
  <p class="mb-1 ms-2" style="font-style: italic; font-size: 1rem; font-weight: 500;">Kéo/Thả để thay đổi thứ tự hiển thị</p>
  <table class="table table-bordered table-hover" id="sliders-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Ảnh</th>
        <th>Sản phẩm</th>
        <th>Thứ tự</th>
        <th style="width: 10rem;">Thao tác</th>
      </tr>
    </thead>
    <tbody>
      @foreach($sliders as $s)
      <tr data-id="{{ $s->id }}">
        <td>{{ $s->id }}</td>
        <td><div class="img-cont"><img src="{{ asset('storage/'.$s->image) }}" alt=""></div></td>
        <td>{{ $s->product->name }}</td>
        {{-- Gắn class sort-handle, style cursor --}}
        <td class="sort-handle" style="cursor: move;">{{ $s->sort_order }}</td>
        <td class="d-flex gap-1">
          <a href="{{ route('admin.product-sliders.edit', $s) }}" class="btn-mimi nut-sua">Sửa</a>
          <form action="{{ route('admin.product-sliders.destroy', $s) }}"
                method="POST" onsubmit="return confirm('Xóa?')">
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
  {{-- style bảng giống trước --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const tbody = document.querySelector('#sliders-table tbody');
  // Lấy URL reorder dưới dạng chuỗi JS an toàn
  const reorderUrl = @json(route('admin.product-sliders.reorder'));

  Sortable.create(tbody, {
    // handle: '.sort-handle',
    animation: 150,
    onEnd: function() {
      const ids = Array.from(tbody.querySelectorAll('tr'))
        .map(tr => tr.getAttribute('data-id'));

      fetch(reorderUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ ids })
      })
      .then(res => res.json())
      .then(json => {
        if (json.status === 'success') {
          tbody.querySelectorAll('tr').forEach((tr, idx) => {
            tr.querySelector('.sort-handle').textContent = idx + 1;
          });
        } else {
          alert('Có lỗi khi lưu thứ tự');
        }
      });
    }
  });
});
</script>
@endpush

