{{-- resources/views/admin/product/create.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="sua-chi-tiet-san-pham">
  <h1 class="mb-4 tieu-de">Thêm sản phẩm mới</h1>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form id="product-form" novalidate 
        action="{{ route('admin.products.store') }}"
        method="POST"
        enctype="multipart/form-data">
    @csrf

    {{-- Thông tin cơ bản --}}
    <div class="ten-va-link">
      <div class="mb-3 ten-va-link-con">
        <label class="form-label">Tên Sản Phẩm</label>
        <input name="name" class="form-control" value="{{ old('name') }}" required>
      </div>
      <div class="mb-3 ten-va-link-con">
        <label class="form-label">Tạo Link</label>
        <input name="slug" class="form-control" value="{{ old('slug') }}" required>
      </div>
      <div class="mb-3 ten-va-link-con">
        <label class="form-label">Base Price</label>
        <input name="base_price" type="number" class="form-control" value="{{ old('base_price') }}" required>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Mô Tả</label>
      <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
    </div>

    <div class="mb-3 w-100">
  <label for="long_description" class="form-label">Mô tả chi tiết</label>
  <textarea id="long_description"
            name="long_description"
            class="form-control"
            rows="10">{{ old('long_description') }}</textarea>
</div>

    <hr>

    {{-- Ảnh chính --}}
    <div class="mb-3">
      <label class="form-label">Ảnh chính</label>
      <input type="file" name="img" class="form-control" accept="image/*" required>
    </div>

    {{-- Ảnh phụ --}}
    <div class="mb-3">
      <label class="form-label">Ảnh phụ (có thể chọn nhiều)</label>
      <input type="file" name="sub_img[]" class="form-control" accept="image/*" multiple>
    </div>

    <hr>
    <h4>Options</h4>
    <div id="options-container"></div>
    <button type="button" id="add-option-btn" class="btn btn-sm nut-them mb-5 me-3">
      + Thêm Phân Loại
    </button>
    {{-- Hiển thị lỗi nếu không có option nào --}}
    <div id="option-error" class="alert alert-danger mb-3" style="display:none;">
      Thêm ít nhất 1 option
    </div>

    <button class="btn mb-5 nut-cap-nhat">Lưu lại</button>
  </form>

  {{-- Template ẩn cho OptionType --}}
  <template id="tpl-option">
    <div class="card mb-3 option-block p-3" data-index="{i}">
      <div class="d-flex justify-content-between align-items-center">
        <h5>Option #{i_display}</h5>
        <button type="button" class="btn btn-sm btn-danger remove-option">–</button>
      </div>
      <div class="mb-2">
        <label class="form-label">Phân Loại</label>
        <input name="options[{i}][name]" class="form-control" required>
      </div>
      <div class="values-container"></div>
      <button type="button"
              class="btn btn-sm nut-them-phan-loai add-value"
              data-opt-index="{i}">
        + Thêm Thuộc Tính
      </button>
    </div>
  </template>

  {{-- Template ẩn cho OptionValue --}}
  <template id="tpl-value">
    <div class="d-flex align-items-end mb-2 value-block" data-val-index="{j}">
      <div class="me-2 flex-fill">
        <label class="form-label">Thuộc Tính Phân Loại</label>
        <input name="options[{i}][values][{j}][value]" class="form-control" required>
      </div>
      <div class="me-2" style="width:120px">
        <label class="form-label">Extra Price</label>
        <input name="options[{i}][values][{j}][extra_price]"
               type="number" step="0.01" class="form-control" required>
      </div>
      <div class="me-2" style="width:150px">
        <label class="form-label">Ảnh</label>
        <input name="options[{i}][values][{j}][option_img]"
               type="file" accept="image/*" class="form-control">
      </div>
      <button type="button" class="btn btn-sm btn-danger remove-value">×</button>
    </div>
  </template>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  let optCount = 0;
  const optionsContainer = document.getElementById('options-container');
  const form = document.getElementById('product-form');
  const optionError = document.getElementById('option-error');

  function addOptionBlock() {
    const tpl = document.getElementById('tpl-option').innerHTML;
    const html = tpl
      .replaceAll('{i}', optCount)
      .replaceAll('{i_display}', optCount + 1);
    const wrapper = document.createElement('div');
    wrapper.innerHTML = html;
    optionsContainer.appendChild(wrapper);
    addValue(optCount);
    optCount++;
  }

  function addValue(i) {
    const block = document.querySelector(`.option-block[data-index="${i}"]`);
    const container = block.querySelector('.values-container');
    const j = container.querySelectorAll('.value-block').length;
    let tpl = document.getElementById('tpl-value').innerHTML;
    tpl = tpl.replaceAll('{i}', i).replaceAll('{j}', j);
    const wrapper = document.createElement('div');
    wrapper.innerHTML = tpl;
    container.appendChild(wrapper);
  }

  function isBlockReady(vb) {
    const valInp   = vb.querySelector('input[name*="[value]"]');
    const extraInp = vb.querySelector('input[name*="[extra_price]"]');
    return valInp.value.trim() !== '' && extraInp.value.trim() !== '';
  }

  // Thêm, xóa option & value
  document.getElementById('add-option-btn').addEventListener('click', addOptionBlock);
  optionsContainer.addEventListener('click', e => {
    if (e.target.matches('.add-value')) {
      addValue(e.target.dataset.optIndex);
    }
    if (e.target.matches('.remove-option')) {
      e.target.closest('.option-block').remove();
    }
    if (e.target.matches('.remove-value')) {
      e.target.closest('.value-block').remove();
    }
  });

  // Auto-add value khi hoàn thiện dòng cuối
  optionsContainer.addEventListener('input', e => {
    if (!e.target.matches('input[name*="[value]"], input[name*="[extra_price]"]')) return;
    const vb = e.target.closest('.value-block');
    const all = Array.from(vb.parentElement.querySelectorAll('.value-block'));
    if (vb === all[all.length - 1] && isBlockReady(vb) && !vb.dataset.handled) {
      vb.dataset.handled = 'true';
      addValue(vb.closest('.option-block').dataset.index);
    }
  });

  // Validate trước submit
  form.addEventListener('submit', function(e) {
    // 1) Phải có ít nhất 1 option
    if (optionsContainer.querySelectorAll('.option-block').length === 0) {
      e.preventDefault();
      optionError.style.display = 'block';
      return;
    }
    optionError.style.display = 'none';

    // 2) Loại bỏ dòng value hoàn toàn trống
    document.querySelectorAll('.value-block').forEach(vb => {
      const valInp   = vb.querySelector('input[name*="[value]"]');
      const extraInp = vb.querySelector('input[name*="[extra_price]"]');
      if (valInp.value.trim() === '' && extraInp.value.trim() === '') {
        vb.remove();
      }
    });
  });
    // === TINYMCE cho mô tả chi tiết ===
  if (window.tinymce) {
    tinymce.init({
      selector: '#long_description',
      height: 400,
      menubar: false,
      plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview', 'anchor',
        'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'paste', 'help', 'wordcount'
      ].join(' '),
      toolbar: [
        'undo redo | fontfamily fontselect | fontsize fontsizeselect | blocks |',
        'bold italic underline strikethrough | alignleft aligncenter alignright alignjustify |',
        'bullist numlist outdent indent | link image media | removeformat | code'
      ].join(' '),
      font_size_formats:  '8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 24pt 36pt 48pt',
      fontsize_formats  : '8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 24pt 36pt 48pt',
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

      images_upload_url: '{{ route("admin.products.uploadImage") }}?_token={{ csrf_token() }}',
      automatic_uploads: true,
      images_upload_credentials: true,

      content_style: `
        @import url('https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;500;600;700;800&display=swap');
        body { font-family: "Baloo 2", cursive; font-size: 14px; }
        img  { max-width: 100%; height: auto; }
      `
    });
  }

});
</script>
@endpush
