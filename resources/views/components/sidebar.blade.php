@php
  use App\Models\SidebarItem;
  $roots = SidebarItem::with('children')->whereNull('parent_id')->orderBy('sort_order')->get();
@endphp

<div class="sb-sidebar">
  <ul class="sb-list">
    @foreach($roots as $item)
      <li class="sb-parent">
        <span class="sb-parent-label">{{ $item->name }}</span>
        @if($item->children->isNotEmpty())
          <button class="sb-toggle" data-target="sb-children-{{ $item->id }}">▾</button>
        @endif
        @if($item->children->isNotEmpty())
          <ul id="sb-children-{{ $item->id }}" class="sb-children">
            @foreach($item->children as $child)
              <li class="sb-child-item">
                <a href="{{ route('collections.show', $child->collection->slug) }}">
                  {{ $child->name }}
                </a>
              </li>
            @endforeach
          </ul>
        @endif
      </li>
    @endforeach
  </ul>
</div>
