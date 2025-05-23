@extends('layouts.app')

{{-- ▼ BẮT ĐẦU CHÈN SIDEBAR --}}
@section('sidebar')
  @include('components.sidebar')
@endsection
{{-- ▲ KẾT THÚC CHÈN SIDEBAR --}}

@section('content')
  {{-- Breadcrumb: Danh mục > Tên Sector --}}
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb bg-white px-0">
      <li class="breadcrumb-item">
        <a href="{{ route('sector.index') }}">Danh mục</a>
      </li>
      <li class="breadcrumb-item active" aria-current="page">
        {{ $sector->name }}
      </li>
    </ol>
  </nav>

  <style>
    h1 {
      font-weight: 700;
      font-size: 3rem;
      color: #b83232;
    }
  </style>

  <div class="container py-4">
    <h1>{{ $sector->name }}</h1>
    <div class="row">
      @foreach($sector->collections as $col)
        <div class="col-6 col-md-4 col-lg-3 text-center mb-4">
          <a href="{{ route('collections.show', $col->slug) }}"
             class="text-decoration-none text-dark">
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
