{{-- resources/views/sectors/show-mobile.blade.php --}}
@extends('layouts.app-mobile')

@section('content')
<style>
    .breadcrumb-item {
  font-style: italic;
  margin-top: 2rem;
}

.breadcrumb-item a {
  text-decoration: none;
  color: #4ab3af;
}

.breadcrumb-item a:hover {
  text-decoration: underline;
}

.breadcrumb-item li {
  color: #4ab3af;
}
.breadcrumb-item+.breadcrumb-item::before {
    float: left;
    padding-right: var(--bs-breadcrumb-item-padding-x);
    color: #4ab3af;
    content: var(--bs-breadcrumb-divider, "/");
}
.sec-cont {
    padding: 0.5rem !important
}
.bread-cont {
    padding-left: 0.5rem;
    padding-top: 1.5rem;
    padding-bottom: 0.3rem;
}
.breadcrumb-item {
    margin: 0 !important;
}
.breadcrumb-item.active {
    color: var(--bs-breadcrumb-item-active-color);
    text-decoration: underline;
}
</style>
  {{-- Breadcrumb: Danh mục > Tên Sector --}}
  <nav aria-label="breadcrumb" class="bg-transparent bread-cont">
    <ol class="breadcrumb m-0 bg-transparent">
      <li class="breadcrumb-item">
        <a href="{{ route('sector.index') }}">Danh mục</a>
      </li>
      <li class="breadcrumb-item active " aria-current="page">
        {{ $sector->name }}
      </li>
    </ol>
  </nav>

  <div class="container sec-cont">
    <div class="row g-3">
      @foreach($sector->collections as $col)
        <div class="col-6">
          <a href="{{ route('collections.show', $col->slug) }}" class="text-decoration-none">
            <div class="card">
              <img src="{{ asset('storage/' . ($col->pivot->custom_image ?? $col->image)) }}" 
                   class="card-img-top" 
                   alt="{{ $col->pivot->custom_name ?? $col->name }}">
              <div class="card-body py-3 px-3">
                <h6 class="card-title text-truncate mb-0 text-center">
                  {{ $col->pivot->custom_name ?? $col->name }}
                </h6>
              </div>
            </div>
          </a>
        </div>
      @endforeach
    </div>
  </div>
@endsection
