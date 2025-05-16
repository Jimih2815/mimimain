@extends('layouts.app-mobile')

@section('title', 'Bộ Sưu Tập Mobile')

@section('content')
@php
    // Lấy danh sách mục cha kèm con → collection → products
    $roots = \App\Models\SidebarItem::with('children.collection.products')
             ->whereNull('parent_id')
             ->orderBy('sort_order')
             ->get();
@endphp

<div class="px-3 py-4">
  <!-- 1) Parent categories -->
  <div id="mobile-parent-bar" class="d-flex overflow-auto">
    @foreach($roots as $parent)
      <div class="mobile-parent-item me-3" data-id="{{ $parent->id }}">
        <h3 class="m-0" style="font-size:1.2rem;">{{ $parent->name }}</h3>
      </div>
    @endforeach
  </div>

  <!-- 2) Child categories (ẩn nếu không có) -->
  <div id="mobile-child-bar" class="d-flex overflow-auto mb-4 tang-2" style="display:none;"></div>

  <!-- 3) Products grid -->
  <div id="mobile-collection-products" class="row gx-2 gy-3">
    {{-- sản phẩm sẽ đổ vào đây bằng JS --}}
  </div>
</div>
@endsection


@push('styles')
<style>
.mobile-parent-item,
.mobile-child-item {
  flex: 0 0 auto;
  padding-right: 1.5rem;
  padding-bottom: 0.5rem;
  /* padding: .5rem 1rem; */
  /* background: #f8f9fa; */
  border-radius: .25rem;
  cursor: pointer;
  white-space: nowrap;
}
.mobile-child-item {
    font-size: 1rem;
    font-weight: 600;
    /* padding: 1rem 0.7rem; */
}
.mobile-parent-item {
    color: #333333;
    /* -webkit-text-stroke: 2px #333333 !important; */
    font-size: 1.2rem;
    font-weight: 600;
    /* padding: 0.2rem 0.6rem; */
}
.mobile-parent-item.active,
.mobile-child-item.active {
  /* background: #4ab3af; */
  color: #4ab3af;
}
#mobile-parent-bar,
#mobile-child-bar {
  display: flex;
  /* hide scrollbars */
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;     /* Firefox */
}
#mobile-parent-bar::-webkit-scrollbar,
#mobile-child-bar::-webkit-scrollbar {
  display: none;             /* Chrome, Safari, Opera */
}
.tang-2 {
    border-bottom: 1px solid #4ab3af;
}
/* Style nút yêu thích trên mobile */
.btn-fav {
  top: 0.5rem;
  right: 0.5rem;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s ease;
  padding: 0;
  background: transparent;
}

.btn-fav:hover {
  background: rgba(255, 255, 255, 1);
}

.btn-fav i {
  font-size: 1.5rem;
  color: #e63946; /* mặc định vàng nhạt */
}

.btn-fav .fas {
  color: #e63946; /* khi đã yêu thích chuyển sang đỏ */
}
.card-body {
  display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: stretch;
}
.gia-tien {
 
  text-align: start;
    font-size: 1.2rem;
    color: #e63946 !important;
}
.gia-tang {
  text-decoration: line-through;
  opacity: 0.7;
  font-size: 0.9rem;
  color: #6c757d;
}
.price-wrapper {
  align-items: flex-start;
    justify-content: space-between;
    flex-direction: column;
}
/* 1. Định nghĩa animation slideDown chung */
@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-15px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* 2. Khi #mobile-child-bar (class tang-2) hiện lên, khung con sẽ trượt */
#mobile-child-bar.tang-2 {
  animation: slideDown 0.2s ease-out;
}

/* 3. Từng .mobile-child-item cũng chạy cùng animation */
#mobile-child-bar.tang-2 .mobile-child-item {
  animation: slideDown 0.2s ease-out;
}

</style>
@endpush

{{-- resources/views/collections/show-mobile.blade.php --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const roots             = @json($roots);
  const parentBar         = document.getElementById('mobile-parent-bar');
  const childBar          = document.getElementById('mobile-child-bar');
  const productsContainer = document.getElementById('mobile-collection-products');
  const favIds            = @json($favIds);

  // ID của collection khi click trực tiếp vào link
  const pageCollectionId = {{ $collection->id }};
  // Mảng products của collection đó
  const pageProducts     = @json($collection->products);

  function renderChildren(parentId) {
    childBar.innerHTML = '';
    productsContainer.innerHTML = '';
    const parent = roots.find(p => p.id == parentId);
    if (!parent || !parent.children.length) {
      childBar.style.display = 'none';
      return;
    }
    childBar.style.display = 'flex';
    parent.children.forEach(child => {
      const div = document.createElement('div');
      div.className = 'mobile-child-item';
      div.dataset.id = child.id;
      div.textContent = child.name;
      div.addEventListener('click', () => {
        childBar.querySelectorAll('.mobile-child-item')
                .forEach(i => i.classList.remove('active'));
        div.classList.add('active');
        renderProducts(child);
      });
      childBar.appendChild(div);
    });
    // auto-click child đầu tiên
    childBar.querySelector('.mobile-child-item').click();
  }

  function renderProducts(source) {
    // source có thể là `child` (vừa click) hoặc object giả { collection:{ products: [...] } }
    const prods = source.collection.products;
    productsContainer.innerHTML = '';

    prods.forEach(prod => {
      const priceText = new Intl.NumberFormat('vi-VN').format(prod.base_price) + '₫';
      // 1.5. Giá tăng 50%
      const increasedText = new Intl.NumberFormat('vi-VN')
      .format(prod.base_price * 1.5) + '₫';
      const col = document.createElement('div');
      col.className = 'col-6';

      const card = document.createElement('div');
      card.className = 'card h-100';

      const imgWrapper = document.createElement('div');
      imgWrapper.className = 'position-relative';

      const link = document.createElement('a');
      link.href = `/products/${prod.slug}`;

      const img = document.createElement('img');
      img.src = `/storage/${prod.img||''}`;
      img.id = `prod-img-${prod.id}`;
      img.className = 'card-img-top';
      img.alt = prod.name;

      link.appendChild(img);
      imgWrapper.appendChild(link);

      const infoDiv = document.createElement('div');
      infoDiv.className = 'card-body p-2 text-center small';

      const nameFavDiv = document.createElement('div');
      nameFavDiv.className = 'd-flex align-items-start justify-content-between mb-2';

      const nameP = document.createElement('p');
      nameP.className = 'mb-1 fw-semibold flex-grow-1 text-start';
      nameP.textContent = prod.name;

      const favBtn = document.createElement('button');
      favBtn.type = 'button';
      favBtn.className = 'btn-fav';
      favBtn.dataset.id = prod.id;

      const favIcon = document.createElement('i');
      if (favIds.includes(prod.id)) {
        favIcon.className = 'fas fa-heart text-danger';
      } else {
        favIcon.className = 'far fa-heart';
      }

      favBtn.appendChild(favIcon);
      nameFavDiv.appendChild(nameP);
      nameFavDiv.appendChild(favBtn);

      // giá gốc
      const priceP = document.createElement('p');
      priceP.className = 'text-danger gia-tien mb-0';
      priceP.textContent = priceText;

      // tạo div mới cho giá tăng 50%
      const incDiv = document.createElement('div');
      incDiv.className = 'text-muted gia-tang';
      incDiv.textContent = increasedText;

      infoDiv.appendChild(nameFavDiv);

      // === giờ swap cho giá gốc lên trước ===
      const priceWrapper = document.createElement('div');
      priceWrapper.className = 'price-wrapper d-flex';
      priceWrapper.appendChild(priceP);   // giá gốc
      priceWrapper.appendChild(incDiv);   // giá tăng
      infoDiv.appendChild(priceWrapper);

      card.appendChild(imgWrapper);
      card.appendChild(infoDiv);
      col.appendChild(card);
      productsContainer.appendChild(col);
    });

    // gán lại sự kiện toggle favorite
    productsContainer.querySelectorAll('.btn-fav').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        fetch(`/favorites/toggle/${id}`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN':
              document.querySelector('meta[name="csrf-token"]').content
          }
        })
        .then(r => r.json())
        .then(json => {
          const icon = btn.querySelector('i');
          icon.classList.toggle('fas', json.added);
          icon.classList.toggle('far', !json.added);
          icon.classList.toggle('text-danger', json.added);
        });
      });
    });
  }

  // gắn sự kiện cho Parent items
  parentBar.querySelectorAll('.mobile-parent-item').forEach(item => {
    item.addEventListener('click', () => {
      parentBar.querySelectorAll('.mobile-parent-item')
               .forEach(i => i.classList.remove('active'));
      item.classList.add('active');
      renderChildren(item.dataset.id);
    });
  });

  // === Phần quan trọng: nếu vào từ link collection cụ thể ===
  if (pageCollectionId) {
    parentBar.style.display = 'none';
    childBar.style.display = 'none';
    // gọi lại renderProducts với object giả để chạy đúng code cũ:
    renderProducts({ collection: { products: pageProducts } });
  } else {
    // logic mặc định: click parent đầu tiên
    if (roots.length) {
      parentBar.querySelector('.mobile-parent-item').click();
    }
  }
});
</script>
@endpush


