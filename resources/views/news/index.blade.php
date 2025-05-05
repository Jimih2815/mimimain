@extends('layouts.app')

@section('content')
<div class="w-50 mx-auto mt-5 mb-5">
  <h1 class="mb-5" style="color:#b83232;">Tin tức mới nhất</h1>

  @foreach($posts as $post)
    <div class="d-flex mb-4 border-bottom pb-3">
      {{-- Ảnh thumbnail như cũ --}}
      @if($post->thumbnail)
        <img src="{{ asset('storage/'.$post->thumbnail) }}"
             class="me-3"
             style="width:200px; height:200px; object-fit:cover;">
      @else
        <div class="bg-secondary me-3"
             style="width:200px; height:200px;"></div>
      @endif

      <div class="flex-fill d-flex justify-content-start flex-column" style="height:200px"> 
        <h5 style="height: 10%;">
          <a href="{{ route('news.show', $post->slug) }}"
             class="text-decoration-none"
             style="color:#4ab3af; font-size:1.5rem;">
            {{ $post->title }}
          </a>
        </h5>
        <small class="text-muted" style="height: 10%;">
          {{ $post->created_at->format('d/m/Y') }}
        </small>

        {{-- Đoạn trích nội dung --}}
        <div class="post-excerpt mt-2"
             style="width:100%; height:150px; overflow:hidden; height: 80%;">
          {!! \Illuminate\Support\Str::limit(
                strip_tags($post->content),
                750,  // hoặc số ký tự bạn muốn
                '...'
              ) !!}
        </div>
      </div>
    </div>
  @endforeach

  {{ $posts->links() }}
</div>
@endsection
