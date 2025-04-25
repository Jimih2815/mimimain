@extends('layouts.admin')

@section('content')
<div class="container-fluid">
  <h1 class="mb-4">Collection Slider</h1>
  <a href="{{ route('admin.collection-sliders.create') }}"
     class="btn btn-primary mb-3">+ Thêm Item</a>

  <table class="table table-striped">
    <thead>
      <tr>
        <th>Thứ tự</th>
        <th>Ảnh</th>
        <th>Text</th>
        <th>Collection</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      @foreach($items as $it)
      <tr>
        <td>{{ $it->sort_order }}</td>
        <td>
          <img src="{{ asset('storage/'.$it->image) }}"
               width="60" class="rounded">
        </td>
        <td>{{ $it->text }}</td>
        <td>{{ $it->collection->name }}</td>
        <td class="d-flex gap-1">
          <form action="{{ route('admin.collection-sliders.move',[$it,'dir'=>'up']) }}"
                method="POST">@csrf
            <button class="btn btn-sm btn-secondary">▲</button>
          </form>
          <form action="{{ route('admin.collection-sliders.move',[$it,'dir'=>'down']) }}"
                method="POST">@csrf
            <button class="btn btn-sm btn-secondary">▼</button>
          </form>
          <a href="{{ route('admin.collection-sliders.edit',$it) }}"
             class="btn btn-sm btn-warning">Sửa</a>
          <form action="{{ route('admin.collection-sliders.destroy',$it) }}"
                method="POST"
                onsubmit="return confirm('Xóa hả?');">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-danger">Xóa</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
