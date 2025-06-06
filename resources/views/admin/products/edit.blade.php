@extends('layouts.admin')

@section('content')
<div class="sua-chi-tiet-san-pham">
  <a href="{{ route('admin.products.index') }}" class="btn nut-quay-ve mb-3">
    <i class="fa-solid fa-chevron-left"></i> Danh sách sản phẩm
  </a>
  <h1 class="mb-4 tieu-de">Sửa sản phẩm #{{ $product->id }}</h1>
  <!-- @if(session('success'))
  <div class="alert alert-success">
    {{ session('success') }}
  </div>
  @endif -->
  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form novalidate
        action="{{ route('admin.products.update', $product) }}"
        method="POST"
        enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- Thông tin cơ bản --}}
    <div class="ten-va-link">
      <div class="mb-3  ten-va-link-con">
        <label class="form-label">Tên Sản Phẩm</label>
        <input name="name"
              class="form-control"
              value="{{ old('name', $product->name) }}"
              required>
      </div>

      <div class="mb-3  ten-va-link-con">
        <label class="form-label">Tạo Link</label>
        <input name="slug"
              class="form-control"
              value="{{ old('slug', $product->slug) }}"
              required>
      </div>

      <div class="mb-3 ten-va-link-con">
        <label class="form-label">Base Price</label>
        <input
          name="base_price"
          type="number"
          class="form-control"
          value="{{ old('base_price', $product->base_price) }}"
          step="any"    {{-- cho phép nhập bất kỳ số thập phân hay nguyên nào --}}
          min="0"        {{-- ngăn nhập số âm nếu bạn cần --}}
          required
        >
      </div>
      

    </div>


    <div class="mb-3">
      <label class="form-label">Mô Tả</label>
      <textarea name="description"
                class="form-control"
                rows="3">{{ old('description', $product->description) }}</textarea>
    </div>

    <div class="mb-3 w-100">
      <label for="long_description" class="form-label">Mô tả chi tiết</label>
      <textarea id="long_description"
                name="long_description"
                class="form-control"
                rows="10">{{ old('long_description', $product->long_description) }}</textarea>
    </div>
    

    <hr>

    {{-- Ảnh chính --}}
    <div class="mb-3">
      <label class="form-label">Ảnh chính</label>
      <input type="file"
             name="img"
             class="form-control"
             accept="image/*">
      @if($product->img)
        <div class="mt-2">
          <img src="{{ asset('storage/'.$product->img) }}"
               width="150"
               style="object-fit:cover"
               alt="Main Image">
        </div>
      @endif
    </div>

    {{-- Ảnh phụ --}}
    <div class="mb-3">
      <label class="form-label">Ảnh phụ (có thể chọn nhiều)</label>
      <input type="file"
             name="sub_img[]"
             class="form-control"
             accept="image/*"
             multiple>
      @if(!empty($product->sub_img))
        <div class="mt-2 d-flex flex-wrap gap-2">
          @foreach($product->sub_img as $sub)
            <img src="{{ asset('storage/'.$sub) }}"
                 width="80" height="80"
                 style="object-fit:cover;border:1px solid #ccc;"
                 alt="Sub Image">
          @endforeach
        </div>
      @endif
    </div>

    <hr>
    <h4>Các Phân Loại</h4>
    <div id="options-container">
      @php
        $groups = $product->optionValues->groupBy(fn($v)=> $v->type->id);
      @endphp
      @foreach($groups as $typeId => $vals)
        @php $i = $loop->index; @endphp
        <div class="card mb-3 option-block p-3" data-index="{{ $i }}">
          <div class="d-flex justify-content-between align-items-center">
            <h5>Option #{{ $i + 1 }}</h5>
            <button type="button" class="btn btn-sm btn-danger remove-option">–</button>
          </div>

          <div class="mb-2">
            <label class="form-label">Tên Phân loại</label>
            <input name="options[{{ $i }}][name]"
                   class="form-control"
                   value="{{ old("options.$i.name", $vals->first()->type->name) }}"
                   required>
          </div>

          <div class="values-container">
          @foreach($vals as $j => $val)
            <div class="d-flex align-items-end mb-2 value-block" data-val-index="{{ $j }}">
              {{-- 1) Cột preview --}}
              <div class="me-2 text-center" style="width:100px">
                @if($val->option_img)
                  <div class="img-cont">
                    <img src="{{ asset('storage/'.$val->option_img) }}" alt="Option img">
                  </div>
                  {{-- giữ lại đường dẫn cũ --}}
                  <input type="hidden"
                        name="options[{{ $i }}][values][{{ $j }}][existing_img]"
                        value="{{ $val->option_img }}">
                @endif
              </div>

              {{-- 2) Cột input file --}}
              <div class="me-2" style="width:150px">
                <label class="form-label">Ảnh</label>
                <input type="file"
                      name="options[{{ $i }}][values][{{ $j }}][option_img]"
                      accept="image/*"
                      class="form-control">
              </div>

              {{-- 3) Cột Giá trị --}}
              <div class="me-2 flex-fill">
                <label class="form-label">Thuộc tính Phân loại</label>
                <input name="options[{{ $i }}][values][{{ $j }}][value]"
                      class="form-control"
                      value="{{ old("options.$i.values.$j.value", $val->value) }}"
                      required>
              </div>

              {{-- 4) Cột Extra Price --}}
              <div class="me-2" style="width:120px">
                <label class="form-label">Extra Price</label>
                <input name="options[{{ $i }}][values][{{ $j }}][extra_price]"
                      type="number" step="0.01"
                      class="form-control"
                      value="{{ old("options.$i.values.$j.extra_price", $val->extra_price) }}"
                      required>
              </div>

              {{-- 5) Nút xóa --}}
              <button type="button" class="btn btn-sm btn-danger remove-value">×</button>
            </div>
          @endforeach

          </div>

          <button type="button"
                  class="btn btn-sm nut-them-phan-loai add-value"
                  data-opt-index="{{ $i }}">
            + Thêm Thuộc Tính
          </button>
        </div>
      @endforeach
    </div>
    <button type="button"
            id="add-option-btn"
            class="btn btn-sm nut-them mb-5 me-3">
      + Thêm Option
    </button>

    <button type="submit" class="btn mb-5 nut-cap-nhat">Cập nhật</button>
  </form>

  {{-- Templates ẩn --}}
  <template id="tpl-option">
    <div class="card mb-3 option-block p-3" data-index="{i}">
      <div class="d-flex justify-content-between align-items-center">
        <h5>Option #{i_display}</h5>
        <button type="button" class="btn btn-sm btn-danger remove-option">–</button>
      </div>
      <div class="mb-2">
        <label class="form-label">Tên Phân Loại</label>
        <input name="options[{i}][name]" class="form-control" required>
      </div>
      <div class="values-container"></div>
      <button type="button"
              class="btn btn-sm add-value nut-them-phan-loai"
              data-opt-index="{i}">
        + Thêm Phân Loại
      </button>
    </div>
  </template>

  <template id="tpl-value">
    <div class="d-flex align-items-end mb-2 value-block" data-val-index="{j}">
      <div class="me-2 flex-fill">
        <label class="form-label">Thuộc tính Phân Loại</label>
        <input name="options[{i}][values][{j}][value]"
               class="form-control"
               required>
      </div>
      <div class="me-2" style="width:120px">
        <label class="form-label">Extra Price</label>
        <input name="options[{i}][values][{j}][extra_price]"
               type="number"
               step="0.01"
               class="form-control"
               required>
      </div>
      <div class="me-2" style="width:150px">
        <label class="form-label">Ảnh Option</label>
        <input name="options[{i}][values][{j}][option_img]"
               type="file"
               accept="image/*"
               class="form-control">
      </div>
      <button type="button" class="btn btn-sm btn-danger remove-value">×</button>
    </div>
  </template>
</div>
@endsection

@push('scripts')
    {{-- Ensure bạn đã include CDN TinyMCE ở layout/admin.blade.php: 
       <script src="https://cdn.tiny.cloud/1/…/tinymce.min.js" referrerpolicy="origin"></script> --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
      // === 1) XỬ LÝ DYNAMIC OPTIONS / VALUES ===
      const optionsContainer = document.getElementById('options-container');
      const addOptionBtn     = document.getElementById('add-option-btn');
      let optionCount        = optionsContainer.querySelectorAll('.option-block').length;
      const tplOption        = document.getElementById('tpl-option').innerHTML;
      const tplValue         = document.getElementById('tpl-value').innerHTML;

      function addValueRow(idx) {
        const block     = optionsContainer.querySelector(`.option-block[data-index="${idx}"]`);
        const container = block.querySelector('.values-container');
        const newJ      = container.querySelectorAll('.value-block').length;
        const html      = tplValue.replace(/{i}/g, idx).replace(/{j}/g, newJ);
        const wrap      = document.createElement('div');
        wrap.innerHTML  = html;
        container.appendChild(wrap.firstElementChild);
      }

      // Khởi tạo nếu block nào chưa có value
      optionsContainer.querySelectorAll('.option-block').forEach(block => {
        const idx = block.dataset.index;
        if (!block.querySelector('.value-block')) addValueRow(idx);
      });

      // Auto-add khi user nhập ở dòng cuối
      optionsContainer.addEventListener('input', function(e) {
        if (!e.target.matches('input[name*="[value]"], input[name*="[extra_price]"]')) return;
        const vb     = e.target.closest('.value-block');
        const sibs   = vb.parentNode.querySelectorAll('.value-block');
        const isLast = vb === sibs[sibs.length - 1];
        const hasVal = vb.querySelector('input[name*="[value]"]').value.trim() !== '';
        const hasPr  = vb.querySelector('input[name*="[extra_price]"]').value.trim() !== '';
        if (isLast && (hasVal || hasPr)) addValueRow(vb.closest('.option-block').dataset.index);
      });

      // Thêm Option mới
      addOptionBtn.addEventListener('click', function() {
        const html = tplOption
          .replace(/{i}/g, optionCount)
          .replace(/{i_display}/g, optionCount + 1);
        const wrap = document.createElement('div');
        wrap.innerHTML = html;
        optionsContainer.appendChild(wrap.firstElementChild);
        addValueRow(optionCount);
        optionCount++;
      });

      // Delegate click: add-value, remove-option, remove-value
      optionsContainer.addEventListener('click', function(e) {
        if (e.target.matches('.add-value')) {
          addValueRow(e.target.dataset.optIndex);
        }
        if (e.target.matches('.remove-option')) {
          e.target.closest('.option-block').remove();
        }
        if (e.target.matches('.remove-value')) {
          e.target.closest('.value-block').remove();
        }
      });

      // Trước khi submit: loại bỏ value-block trống
      const form = optionsContainer.closest('form');
      form.addEventListener('submit', function() {
        optionsContainer.querySelectorAll('.value-block').forEach(vb => {
          const hasVal = vb.querySelector('input[name*="[value]"]').value.trim() !== '';
          const hasPr  = vb.querySelector('input[name*="[extra_price]"]').value.trim() !== '';
          if (!hasVal && !hasPr) vb.remove();
        });
      });

      // === 2) KHỞI TẠO TINYMCE ===
      if (!window.tinymce) return;

      tinymce.init({
        selector: '#long_description',
        height: 400,
        menubar: false,

        /* ――― Plugins (không cần 'font') ――― */
        plugins: [
          'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview', 'anchor',
          'searchreplace', 'visualblocks', 'code', 'fullscreen',
          'insertdatetime', 'media', 'table', 'paste', 'help', 'wordcount'
        ].join(' '),

        /* ――― Toolbar (TinyMCE 6 đổi tên) ――― */
        toolbar: [
          'undo redo | fontfamily fontselect | fontsize fontsizeselect | blocks |',
          'bold italic underline strikethrough | alignleft aligncenter alignright alignjustify |',
          'bullist numlist outdent indent | link image media | removeformat | code'
        ].join(' '),

        /* ――― Dropdown kích cỡ chữ ――― */
        font_size_formats:  '8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 24pt 36pt 48pt',
        fontsize_formats  : '8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 24pt 36pt 48pt', // fallback TinyMCE 5

        /* ――― Dropdown font-family (thêm Baloo 2) ――― */
        font_family_formats: [
          'Baloo 2="Baloo 2",cursive',
          'Arial=Arial,Helvetica,sans-serif',
          'Helvetica=Helvetica,Arial,sans-serif',
          'Verdana=Verdana,Geneva,sans-serif',
          'Tahoma=Tahoma,Arial,sans-serif',
          'Times New Roman=Times New Roman,serif',
          'Georgia=Georgia,serif',
          'Courier New=Courier New,courier'
        ].join(';'),

        /* ――― Uploader ảnh kèm CSRF (Laravel) ――― */
        images_upload_url: '{{ route("admin.products.uploadImage") }}?_token={{ csrf_token() }}',
        automatic_uploads: true,
        images_upload_credentials: true,

        /* ――― CSS trong iframe editor ――― */
        content_style: `
          /* Nạp Google Font ngay trong iframe để chắc cú */
          @import url('https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;500;600;700;800&display=swap');

          body { font-family: "Baloo 2", cursive; font-size: 14px; }
          img  { max-width: 100%; height: auto; }
        `
      });
    });
</script>
@endpush


