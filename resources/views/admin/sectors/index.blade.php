@extends('layouts.admin')

@section('content')
<style>
   .container-fluid {
        width: 60%;
    }

</style>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1>Ngành hàng</h1>
  <a href="{{ route('admin.sectors.create') }}" class="btn-mimi nut-xanh text-decoration-none">Thêm mới</a>
</div>

<table class="table table-bordered" id="sortable-sector-table">
  <thead>
    <tr>
      <th style="width: 5rem;">Thứ tự</th>
      <th style="width: 9rem;">Ảnh</th>
      <th>Tên</th>
      <th>Collection</th>
      
      <th style="width: 8rem;">Hành động</th>
    </tr>
  </thead>
  <tbody id="sortable-sector-body">
    @foreach($sectors as $s)
    <tr data-id="{{ $s->id }}">
      <td>{{ $s->sort_order }}</td>
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
      
      <td>
        <a href="{{ route('admin.sectors.edit',$s) }}" class="btn-mimi nut-sua">Sửa</a>
        <form action="{{ route('admin.sectors.destroy', $s) }}" method="POST" class="d-inline">
          @csrf @method('DELETE')
          <button onclick="return confirm('Xác nhận xoá?')" class="btn-mimi nut-xoa">Xóa</button>
        </form>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    new Sortable(document.getElementById('sortable-sector-body'), {
      animation: 150,
      handle: 'td',
      onEnd: function (evt) {
        let order = [];
        document.querySelectorAll('#sortable-sector-body tr').forEach((tr, index) => {
          order.push({
            id: tr.getAttribute('data-id'),
            sort_order: index + 1
          });
        });

        fetch('{{ route('admin.sectors.reorder') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({ order })
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            console.log('Cập nhật thứ tự thành công!');
          }
        });
      }
    });
  });
</script>
@endpush
