@php
  use App\Models\SidebarItem;
  $roots = SidebarItem::with('children')->whereNull('parent_id')->orderBy('sort_order')->get();
@endphp

<div class="sb-sidebar quan-ly-side-bar border-0 bg-transparent">
  <ul class="sb-list ">
    @foreach($roots as $item)
      <li class="sb-parent">
        <div class="cha-va-btn">
          <span class="sb-parent-label">{{ $item->name }}</span>
          @if($item->children->isNotEmpty())
            <button class="sb-toggle" data-target="sb-children-{{ $item->id }}">▾</button>
          @endif
        </div>
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
