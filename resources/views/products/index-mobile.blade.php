@extends('layouts.app-mobile')

@section('title', 'Tất cả Sản phẩm Mobile')

@section('content')
<style>
  /* Ẩn scrollbar */
  #mobile-parent-bar, #mobile-child-bar { -ms-overflow-style:none; scrollbar-width:none; }
  #mobile-parent-bar::-webkit-scrollbar, #mobile-child-bar::-webkit-scrollbar { display:none; }

  /* Không xuống dòng */
  #mobile-parent-bar, #mobile-child-bar { white-space:nowrap; }

  /* Parent active */
  .mobile-parent-item.active h3 { color:#4ab3af; }

  /* Child active */
  .mobile-child-item.active { font-weight:600; color:#4ab3af; }
  .mobile-parent-item,
    .mobile-child-item {
    flex: 0 0 auto;
    padding-right: 1rem;
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
        white-space: nowrap;
    }
    .mobile-parent-item.active,
    .mobile-child-item.active {
    /* background: #4ab3af; */
    color: #4ab3af;
    }
    .tang-2 {
            border-bottom: 1px solid #4ab3af;

    }
    /* 1) Định nghĩa keyframes */
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

    /* 2) Áp animation cho mỗi item khi được chèn */
    #mobile-child-bar .mobile-child-item {
    animation: slideDown 0.3s ease both;
    }

</style>



<div class="px-3 py-4">

  <!-- 1) Thanh chọn collection cha -->
  <div id="mobile-parent-bar" class="d-flex flex-nowrap overflow-auto">
    @foreach($roots as $parent)
      <div class="mobile-parent-item me-3" data-id="{{ $parent->id }}">
        <h3 style="font-size: 1.2rem;" class="m-0" >{{ $parent->name }}</h3>
      </div>
    @endforeach
  </div>

  <!-- 2) Thanh chọn collection con (ẩn khi không có) -->
  <div id="mobile-child-bar" class="d-flex flex-nowrap overflow-auto mb-3 tang-2" style="display:none;"></div>

  <!-- 3) Danh sách sản phẩm -->
  <div id="mobile-collection-products" class="row gx-2 gy-3"></div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', () => {
  const roots = @json($roots->toArray());
  const allProducts = @json($products->toArray());
  const favIds     = @json($favIds); 
  const parentBar = document.getElementById('mobile-parent-bar');
  const childBar = document.getElementById('mobile-child-bar');
  const productsContainer = document.getElementById('mobile-collection-products');

  // Render cards
  function renderProducts(list) {
  productsContainer.innerHTML = '';

  list.forEach(prod => {
    // 1) Column và card
    const col  = document.createElement('div');
    col.className = 'col-6';
    const card = document.createElement('div');
    card.className = 'card h-100';

    // 2) Ảnh + link
    if (prod.img) {
      const a   = document.createElement('a');
      a.href    = `/products/${prod.slug}`;
      const img = document.createElement('img');
      img.src   = `/storage/${prod.img}`;
      img.className = 'card-img-top';
      a.appendChild(img);
      card.appendChild(a);
    }

    // 3) Info container (giống show-mobile)
    const infoDiv = document.createElement('div');
    infoDiv.className = 'card-body p-2 text-center small';

    // 3.1) Tên + Fav
    const nameFavDiv = document.createElement('div');
    nameFavDiv.className = 'd-flex align-items-start justify-content-between mb-2';

    const nameP = document.createElement('p');
    nameP.className = 'mb-1 fw-semibold flex-grow-1 text-start';
    nameP.textContent = prod.name;
    nameFavDiv.appendChild(nameP);

    // Nút yêu thích
    const favBtn = document.createElement('button');
    favBtn.type    = 'button';
    favBtn.className = 'btn-fav';
    favBtn.dataset.id = prod.id;

    const favIcon = document.createElement('i');
    if (favIds.includes(prod.id)) {
      favIcon.className = 'fas fa-heart text-danger';
    } else {
      favIcon.className = 'far fa-heart';
    }
    favBtn.appendChild(favIcon);
    nameFavDiv.appendChild(favBtn);

    infoDiv.appendChild(nameFavDiv);

    // 3.2) Giá gốc và giá tăng 50%
    const priceText     = new Intl.NumberFormat('vi-VN').format(prod.base_price) + '₫';
    const increasedText = new Intl.NumberFormat('vi-VN').format(prod.base_price * 1.5) + '₫';

    const priceP = document.createElement('p');
    priceP.className = 'text-danger gia-tien mb-0';
    priceP.textContent = priceText;

    const incDiv = document.createElement('div');
    incDiv.className = 'text-muted gia-tang';
    incDiv.textContent = increasedText;

    const priceWrapper = document.createElement('div');
    priceWrapper.className = 'price-wrapper d-flex';
    priceWrapper.appendChild(priceP);
    priceWrapper.appendChild(incDiv);
    infoDiv.appendChild(priceWrapper);

    // 4) Kết nối thẻ
    card.appendChild(infoDiv);
    col.appendChild(card);
    productsContainer.appendChild(col);
  });

  // 5) Gắn sự kiện toggle favorite
  productsContainer.querySelectorAll('.btn-fav').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;
      fetch(`/favorites/toggle/${id}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
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



  // Highlight parent
  function setActiveParent(item) {
    parentBar.querySelectorAll('.mobile-parent-item')
      .forEach(el => el.classList.remove('active'));
    item.classList.add('active');
  }
  // Highlight child
  function setActiveChild(item) {
    childBar.querySelectorAll('.mobile-child-item')
      .forEach(el => el.classList.remove('active'));
    item.classList.add('active');
  }

  // Initial: show all products, hide child bar
  renderProducts(allProducts);
  childBar.style.display = 'none';

  // When a parent is clicked
  parentBar.addEventListener('click', e => {
    const item = e.target.closest('.mobile-parent-item');
    if (!item) return;
    setActiveParent(item);

    const parentData = roots.find(p => p.id == item.dataset.id);
    productsContainer.innerHTML = '';
    childBar.innerHTML = '';

    if (!parentData.children.length) {
      childBar.style.display = 'none';
      renderProducts(allProducts);
      return;
    }

    childBar.style.display = 'flex';
    parentData.children.forEach(child => {
      const div = document.createElement('div');
      div.className = 'mobile-child-item me-2';
      div.dataset.collection = child.collection.id;
      div.textContent = child.name;
      childBar.appendChild(div);
    });

    // Automatically select first child under this parent
    const firstChild = childBar.querySelector('.mobile-child-item');
    if (firstChild) {
      setActiveChild(firstChild);
      const productsForFirst = roots
        .flatMap(p => p.children)
        .find(c => c.collection.id == firstChild.dataset.collection)
        .collection.products;
      renderProducts(productsForFirst);
    }
  });

  // When a child is clicked
  childBar.addEventListener('click', e => {
    const item = e.target.closest('.mobile-child-item');
    if (!item) return;
    setActiveChild(item);
    const productsForSelected = roots
      .flatMap(p => p.children)
      .find(c => c.collection.id == item.dataset.collection)
      .collection.products;
    renderProducts(productsForSelected);
  });
});
</script>


