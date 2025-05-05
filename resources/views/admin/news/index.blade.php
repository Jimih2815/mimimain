@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
  <h1>Quản lý News</h1>
  <a href="{{ route('admin.news.create') }}" class="btn btn-primary mb-3">Thêm bài viết</a>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <table class="table table-bordered">
    <thead><tr>
      <th>ID</th><th>Tiêu đề</th><th>Ngày tạo</th><th>Hành động</th>
    </tr></thead>
    <tbody>
      @foreach($posts as $p)
      <tr>
        <td>{{ $p->id }}</td>
        <td>{{ $p->title }}</td>
        <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
        <td>
          <a href="{{ route('admin.news.edit',$p) }}" class="btn btn-sm btn-warning">Sửa</a>
          <form action="{{ route('admin.news.destroy',$p) }}" method="POST" class="d-inline-block">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-danger" onclick="return confirm('Xóa?')">Xóa</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

  {{ $posts->links() }}
</div>
@endsection
