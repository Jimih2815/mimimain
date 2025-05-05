@extends('layouts.admin')

@section('content')

<div class="container-fluid py-4">
  <h1 class="mb-5" style="color:#b83232;">Quản lý News</h1>
  <a href="{{ route('admin.news.create') }}" class="btn-mimi nut-xanh mb-3 mt-5 text-decoration-none">Thêm bài viết</a>

  {{-- Chọn collection global (nếu bạn muốn áp dụng cho trang public index) --}}
  <form action="{{ route('admin.news.selectCollection') }}" method="POST" class="mb-4">
    @csrf
    <div class="d-flex align-items-center mt-4">
      <label class="me-2 mb-0">Collection hiển thị ở trang Tin Tức:</label>
      <select name="collection_id" class="form-select w-auto"
              onchange="this.form.submit()">
        <option value="">-- Không chọn --</option>
        @foreach($collections as $col)
          <option value="{{ $col->id }}"
            {{ (optional($selectedCollection)->id==$col->id)? 'selected':'' }}>
            {{ $col->name }}
          </option>
        @endforeach
      </select>
    </div>
  </form>

  <table class="table table-bordered text-center">
    <thead>
      <tr>
        <th>ID</th>
        <th>Tiêu đề</th>
        <th>Collection</th> {{-- cột mới --}}
        <th>Ngày tạo</th>
        <th>Hành động</th>
      </tr>
    </thead>
    <tbody>
      @foreach($posts as $p)
      <tr>
        <td>{{ $p->id }}</td>
        <td>{{ $p->title }}</td>
        {{-- assign từng bản tin vào collection --}}
        <td>
          <form action="{{ route('admin.news.assignCollection', $p) }}"
                method="POST">
            @csrf
            <select name="collection_id"
                    class="form-select form-select-sm"
                    onchange="this.form.submit()">
              <option value="">--None--</option>
              @foreach($collections as $col)
                <option value="{{ $col->id }}"
                  {{ ($p->collection_id==$col->id)? 'selected':'' }}>
                  {{ $col->name }}
                </option>
              @endforeach
            </select>
          </form>
        </td>
        <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
        <td>
          <a href="{{ route('admin.news.edit',$p) }}"
             class="btn-mimi nut-vang text-decoration-none">Sửa</a>
          <form action="{{ route('admin.news.destroy',$p) }}"
                method="POST"
                class="d-inline-block">
            @csrf @method('DELETE')
            <button class="btn-mimi nut-xanh-la text-decoration-none"
                    onclick="return confirm('Xóa?')">Xóa</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

  {{ $posts->links() }}
</div>
@endsection
@push('styles')
<style>
  thead th {
    background-color: #4ab3af !important;
    color: white !important;

  }
  .nut-vang, .nut-xanh-la {
    font-size: 1rem;
    padding: 0.3rem 1rem;
  }
</style>
@endpush