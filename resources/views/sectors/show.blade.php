@extends('layouts.app')

@section('content')
  <div class="container py-4">
    <h1>{{ $sector->name }}</h1>
    <div class="row">
      @foreach($sector->collections as $col)
        <div class="col-6 col-md-4 col-lg-3 text-center mb-4">
          {{-- Link về trang collection đúng slug --}}
          <a href="{{ route('collections.show', $col->slug) }}"
             class="text-decoration-none text-dark">
             
            {{-- Ảnh hiển thị: ưu tiên custom_image, nếu không có thì lấy ảnh gốc của collection --}}
            <img src="{{ asset('storage/' . ($col->pivot->custom_image ?? $col->image)) }}"
                 class="img-fluid rounded mb-2"
                 alt="{{ $col->pivot->custom_name ?? $col->name }}">

            <h5 class="mb-0">
              {{ $col->pivot->custom_name ?? $col->name }}
            </h5>
          </a>
        </div>
      @endforeach
    </div>
  </div>
@endsection
