@extends('layouts.admin')

@section('content')
<div class="container-fluid trang-edit-home">
 

  <h1 class="mb-4 tieu-de">Quản lý Trang Chủ</h1>

 

  <form action="{{ route('admin.home.update') }}"
        method="POST"
        enctype="multipart/form-data">
    @csrf
    <button class="btn btn-luu">Lưu thay đổi</button>
    {{-- A) Phần trước Banner --}}
    <h1 class="mt-4">Phần trước Banner</h1>
    <div class="mb-3">
      <label class="form-label">Tiêu đề (H3)</label>
      <input type="text"
             name="pre_banner_title"
             value="{{ old('pre_banner_title', $home->pre_banner_title) }}"
             class="form-control full-width">
      @error('pre_banner_title')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">Nội dung nút trước Banner</label>
      <input type="text"
             name="pre_banner_button_text"
             value="{{ old('pre_banner_button_text', $home->pre_banner_button_text) }}"
             class="form-control full-width">
      @error('pre_banner_button_text')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">Chọn Collection cho nút trước Banner</label>
      <select name="pre_banner_button_collection_id" class="form-select">
        <option value="">-- Không chọn --</option>
        @foreach($collections as $id => $name)
          <option value="{{ $id }}"
            @selected(old('pre_banner_button_collection_id', $home->pre_banner_button_collection_id)==$id)>
            {{ $name }}
          </option>
        @endforeach
      </select>
      @error('pre_banner_button_collection_id')<div class="text-danger">{{ $message }}</div>@enderror
    </div>

    

    <hr>
    {{-- C) Banner slider & nút trung tâm --}}
    <h1 class="margintop6rem">Banner Slider (Desktop/Mobile) + nút trung tâm</h1>

    {{-- Upload nhiều ảnh desktop --}}
    <div class="mt-3 mb-3">
      <label class="form-label">Thêm ảnh Banner Desktop (có thể chọn nhiều ảnh 1 lần)</label>
      <input type="file"
             name="banner_images_desktop[]"
             class="form-control full-width"
             accept="image/*"
             multiple>
      @error('banner_images_desktop')<div class="text-danger">{{ $message }}</div>@enderror
      @error('banner_images_desktop.*')<div class="text-danger">{{ $message }}</div>@enderror
      <p class="text-muted mt-2" style="font-style: italic;">Kéo/Thả để đổi thứ tự. Mỗi ảnh có thể gắn 1 collection riêng.</p>
    </div>

    <div class="mb-4">
      <table id="home-banner-desktop-table" class="table table-striped align-middle">
        <thead>
          <tr>
            <th class="home-banner-col-order">STT</th>
            <th>Ảnh</th>
            <th class="home-banner-col-collection">Collection khi bấm</th>
            <th class="home-banner-col-delete">Xóa</th>
          </tr>
        </thead>
        <tbody>
          @forelse($bannerImagesDesktop as $b)
            <tr data-id="{{ $b->id }}">
              <td class="sort-handle home-banner-sort-cell">{{ $b->sort_order }}</td>
              <td>
                <img src="{{ asset('storage/'.$b->image) }}"
                     alt="banner"
                     class="home-banner-preview home-banner-preview-desktop">
              </td>
              <td>
                <select name="banner_items_desktop[{{ $b->id }}][collection_id]" class="form-select">
                  <option value="">-- Không chọn (chỉ xem ảnh) --</option>
                  @foreach($collections as $id => $name)
                    <option value="{{ $id }}" @selected((string)($b->collection_id ?? '') === (string)$id)>{{ $name }}</option>
                  @endforeach
                </select>
              </td>
              <td class="home-banner-delete-cell">
                <input type="checkbox" name="banner_items_desktop[{{ $b->id }}][delete]" value="1">
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted">Chưa có banner desktop nào.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Upload nhiều ảnh mobile --}}
    <div class="mt-3 mb-3">
      <label class="form-label">Thêm ảnh Banner Mobile (dọc, có thể chọn nhiều ảnh 1 lần)</label>
      <input type="file"
             name="banner_images_mobile[]"
             class="form-control full-width"
             accept="image/*"
             multiple>
      @error('banner_images_mobile')<div class="text-danger">{{ $message }}</div>@enderror
      @error('banner_images_mobile.*')<div class="text-danger">{{ $message }}</div>@enderror
      <p class="text-muted mt-2" style="font-style: italic;">Nếu có banner mobile thì trang chủ mobile sẽ ưu tiên dùng banner mobile.</p>
    </div>

    <div class="mb-4">
      <table id="home-banner-mobile-table" class="table table-striped align-middle">
        <thead>
          <tr>
            <th class="home-banner-col-order">STT</th>
            <th>Ảnh</th>
            <th class="home-banner-col-collection">Collection khi bấm</th>
            <th class="home-banner-col-delete">Xóa</th>
          </tr>
        </thead>
        <tbody>
          @forelse($bannerImagesMobile as $b)
            <tr data-id="{{ $b->id }}">
              <td class="sort-handle home-banner-sort-cell">{{ $b->sort_order }}</td>
              <td>
                <img src="{{ asset('storage/'.$b->image) }}"
                     alt="banner"
                     class="home-banner-preview home-banner-preview-mobile">
              </td>
              <td>
                <select name="banner_items_mobile[{{ $b->id }}][collection_id]" class="form-select">
                  <option value="">-- Không chọn (chỉ xem ảnh) --</option>
                  @foreach($collections as $id => $name)
                    <option value="{{ $id }}" @selected((string)($b->collection_id ?? '') === (string)$id)>{{ $name }}</option>
                  @endforeach
                </select>
              </td>
              <td class="home-banner-delete-cell">
                <input type="checkbox" name="banner_items_mobile[{{ $b->id }}][delete]" value="1">
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted">Chưa có banner mobile nào.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- hidden để luôn gửi show_button về controller --}}
    <input type="hidden" name="show_button" value="0">
    <div class="mb-3 form-check">
      <input type="checkbox"
             name="show_button"
             id="show_button"
             class="form-check-input custom-checkbox"
             value="1"
             @checked(old('show_button', $home->show_button))>
      <label for="show_button" class="form-check-label">
        Hiển thị nút trung tâm
      </label>
    </div>

    <div class="mb-3">
      <label class="form-label">Nội dung nút trung tâm</label>
      <input type="text"
             name="button_text"
             value="{{ old('button_text', $home->button_text) }}"
             class="form-control full-width">
      @error('button_text')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">Chọn Collection cho nút trung tâm</label>
      <select name="button_collection_id" class="form-select">
        <option value="">-- Không chọn --</option>
        @foreach($collections as $id => $name)
          <option value="{{ $id }}"
            @selected(old('button_collection_id', $home->button_collection_id)==$id)>
            {{ $name }}
          </option>
        @endforeach
      </select>
      @error('button_collection_id')<div class="text-danger">{{ $message }}</div>@enderror
    </div>


    



    <hr>
    {{-- D) Phần Khởi Đầu (lead + nút “Xem chi tiết”) --}}
    <h1 class="margintop6rem">Sửa phần text và link dưới Banner </h1>
    <div class="mb-3">
      <label class="form-label">Nội dung lead (p.lead)</label>
      <input type="text"
             name="intro_text"
             value="{{ old('intro_text', $home->intro_text) }}"
             class="form-control full-width">
      @error('intro_text')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">Nội dung nút khởi đầu</label>
      <input type="text"
             name="intro_button_text"
             value="{{ old('intro_button_text', $home->intro_button_text) }}"
             class="form-control full-width">
      @error('intro_button_text')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">Chọn Collection cho nút khởi đầu</label>
      <select name="intro_button_collection_id" class="form-select">
        <option value="">-- Không chọn --</option>
        @foreach($collections as $id => $name)
          <option value="{{ $id }}"
            @selected(old('intro_button_collection_id', $home->intro_button_collection_id)==$id)>
            {{ $name }}
          </option>
        @endforeach
      </select>
      @error('intro_button_collection_id')<div class="text-danger">{{ $message }}</div>@enderror
    </div>

      <!--  THÊM PREVIEW VÀ CHỈNH SỬA COLLECTION SLIDER Ở ĐÂY -->
      <hr>
      <h1 class="margintop6rem">Preview và sửa Collection Slider</h1>

    <h5 class="mt-4">Preview Collection Slider</h5>
    <div class="d-flex overflow-auto py-2 collection-slider">
      @foreach($collectionSliders as $slider)
        <div class="flex-shrink-0 me-3" style="width:200px;">
          <div class="card">
            <img src="{{ asset('storage/'.$slider->image) }}"
                 class="card-img-top" alt="">
            <div class="card-body p-2 text-center">
              <small class="text-muted">{{ $slider->text }}</small>
            </div>
          </div>
        </div>
      @endforeach
    </div>
    <a href="{{ route('admin.collection-sliders.index') }}"
       class="btn btn-slide mb-4">
      Chỉnh sửa Collection Slider
    </a>



    
    <hr>
    {{-- B) Phần Bộ Sưu Tập --}}
    <h1 class="margintop6rem">Sửa link và text Bộ Sưu Tập</h1>

    <div class="mb-3">
      <label class="form-label">Tiêu đề bộ sưu tập (H2)</label>
      <input type="text"
             name="collection_section_title"
             value="{{ old('collection_section_title', $home->collection_section_title) }}"
             class="form-control full-width">
      @error('collection_section_title')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">Nội dung nút bộ sưu tập</label>
      <input type="text"
             name="collection_section_button_text"
             value="{{ old('collection_section_button_text', $home->collection_section_button_text) }}"
             class="form-control full-width">
      @error('collection_section_button_text')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">Chọn Collection cho nút bộ sưu tập</label>
      <select name="collection_section_button_collection_id" class="form-select">
        <option value="">-- Không chọn --</option>
        @foreach($collections as $id => $name)
          <option value="{{ $id }}"
            @selected(old('collection_section_button_collection_id', $home->collection_section_button_collection_id)==$id)>
            {{ $name }}
          </option>
        @endforeach
      </select>
      @error('collection_section_button_collection_id')<div class="text-danger">{{ $message }}</div>@enderror
    </div>


    <!-- THÊM PREVIEW VÀ CHỈNH SỬA admin/home-section-images VÀO ĐÂY -->
    <hr>
    <h1 class="margintop6rem mb-3">Preview và sửa Banner 2 ảnh</h1>
    <div class="d-flex mb-4 banner-2-cont">
      @foreach($homeSectionImages->take(2) as $sectionImage)
        <div class="card">
          <img src="{{ asset('storage/'.$sectionImage->image) }}"
               class="card-img-top"
               alt="Home Section Image">
        </div>
      @endforeach
    </div>
    <a href="{{ url('/admin/home-section-images') }}"
       class="btn btn-slide mb-4">
      Chỉnh sửa Banner 2 ảnh
    </a>





    <!-- THÊM PREVIEW VÀ CHỈNH SỬA PRODUCT SLIDER Ở ĐÂY -->
    <hr>
    <h1 class="margintop6rem">Preview và sửa Collection Slider</h1>

    <div class="d-flex overflow-auto py-2 product-slider">
      @foreach($productSliders as $slider)
        <div class="flex-shrink-0 me-3" style="width:200px;">
          <div class="card">
            <div class="image-wrapper">  <img src="{{ asset('storage/'.$slider->image) }}"
                  class="card-img-top" alt=""> </div>
            <div class="card-body p-2 text-center">
              <small class="text-muted">{{ $slider->product->name }}</small>
            </div>
          </div>
        </div>
      @endforeach
    </div>
    <a href="{{ route('admin.product-sliders.index') }}"
       class="btn btn-slide mb-4">
      Chỉnh sửa Product Slider
    </a>




    <hr>
    {{-- E) Giới thiệu (About) --}}
    <h1 class="margintop6rem">Sửa phần Giới thiệu Shop</h1>

    <div class="mt-3">
      <label class="form-label">Tiêu đề Giới thiệu</label>
      <input type="text"
             name="about_title"
             value="{{ old('about_title', $home->about_title) }}"
             class="form-control full-width">
      @error('about_title')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
      <label class="form-label">Nội dung Giới thiệu</label>
      <textarea name="about_text"
                class="form-control full-width"
                rows="4">{{ old('about_text', $home->about_text) }}</textarea>
      @error('about_text')<div class="text-danger">{{ $message }}</div>@enderror
    </div>

  </form>
</div>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const reorderUrl = @json(route('admin.home-banners.reorder'));

      function initSortable(tableSelector, device) {
        const tbody = document.querySelector(`${tableSelector} tbody`);
        if (!tbody) return;

        Sortable.create(tbody, {
          animation: 150,
          handle: '.sort-handle',
          onEnd: function () {
            const ids = Array.from(tbody.querySelectorAll('tr[data-id]'))
              .map(tr => tr.dataset.id);

            fetch(reorderUrl, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
              },
              body: JSON.stringify({ device, ids })
            })
            .then(r => r.json())
            .then(json => {
              if (json.status === 'success') {
                tbody.querySelectorAll('tr[data-id]').forEach((tr, idx) => {
                  const cell = tr.querySelector('.sort-handle');
                  if (cell) cell.textContent = idx + 1;
                });
              } else {
                alert('Lỗi lưu thứ tự banner');
              }
            })
            .catch(() => alert('Không thể lưu thứ tự banner (mạng / CSRF).'));
          }
        });
      }

      initSortable('#home-banner-desktop-table', 'desktop');
      initSortable('#home-banner-mobile-table', 'mobile');
    });
  </script>
@endpush
