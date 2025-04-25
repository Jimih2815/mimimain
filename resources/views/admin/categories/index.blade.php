@extends('layouts.app')
@section('title','Admin – Categories')

@section('content')
<div class="container py-4">

{{-- ========== Add Category ========== --}}
<form class="d-flex gap-2 mb-4" method="POST" action="{{ route('admin.categories.store') }}">
  @csrf
  <input name="name"  class="form-control" placeholder="Tên category">
  <button class="btn btn-primary">➕ Thêm</button>
</form>

{{-- ========== List ========== --}}
@foreach($cats as $c)
  <div class="card mb-3">
    <div class="card-header d-flex justify-content-between">
      <strong>{{ $c->name }}</strong>
      <form method="POST" action="{{ route('admin.categories.destroy',$c) }}">
        @csrf @method('DELETE')
        <button class="btn btn-sm btn-danger">X</button>
      </form>
    </div>

    <div class="card-body">

      {{-- Groups của category --}}
      @foreach($c->groups as $g)
        <div class="border p-2 mb-2">
          <form class="d-flex align-items-center gap-2" method="POST"
                action="{{ route('admin.categories.groups.update',$g) }}">
            @csrf @method('PUT')
            <input name="title" value="{{ $g->title }}" class="form-control" style="max-width:250px">
            <input name="sort_order" value="{{ $g->sort_order }}" class="form-control" style="width:90px">
            <button class="btn btn-sm btn-success">💾</button>
            <a href="{{ route('admin.categories.groups.destroy',$g) }}"
               onclick="event.preventDefault();this.nextElementSibling.submit()" class="btn btn-sm btn-outline-danger">🗑</a>
            <form method="POST" action="{{ route('admin.categories.groups.destroy',$g) }}" class="d-none">@csrf @method('DELETE')</form>
          </form>

          {{-- Product picker --}}
          <form class="d-flex gap-2 mt-2" method="POST"
                action="{{ route('admin.categories.groups.product.attach',$g) }}">
            @csrf
            <select name="pid" class="form-select product-select" style="min-width:250px">
              @foreach($allProducts as $p)
                <option value="{{ $p->id }}">{{ $p->name }}</option>
              @endforeach
            </select>
            <button class="btn btn-sm btn-primary">+ SP</button>
          </form>

          {{-- SP list --}}
          <ul class="mt-2">
            @foreach($g->products as $p)
              <li class="d-flex justify-content-between">
                {{ $p->name }}
                <form method="POST"
                      action="{{ route('admin.categories.groups.product.detach',[$g,$p->id]) }}">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-link text-danger">X</button>
                </form>
              </li>
            @endforeach
          </ul>

        </div>
      @endforeach

      {{-- Add new group --}}
      <form class="d-flex gap-2" method="POST"
            action="{{ route('admin.categories.groups.store',$c) }}">
        @csrf
        <input name="title" placeholder="Tiêu đề dropdown mới" class="form-control">
        <button class="btn btn-sm btn-secondary">➕ Thêm mục</button>
      </form>

    </div>
  </div>
@endforeach
</div>
@endsection

@push('scripts')
  {{-- nếu muốn search AJAX: gắn select2 --}}
  <script>
    $('.product-select').select2({
      width:'resolve'
    });
  </script>
@endpush
