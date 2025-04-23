@extends('layouts.app')

@section('title', 'Kết quả tìm kiếm')

@section('content')
<div class="container py-5">
  <h2 class="mb-4">Kết quả tìm kiếm cho: '{{ $q }}'</h2>

  @if(trim($q) === '' || $results->isEmpty())
    <div class="alert alert-warning">
      Không tìm thấy kết quả phù hợp.
    </div>
  @else
    <div class="list-group">
      @foreach($results as $item)
        <a
          href="{{ route('products.show', $item->id) }}"
          class="list-group-item list-group-item-action"
        >
          {{ $item->name }}
        </a>
      @endforeach
    </div>
  @endif
</div>
@endsection
