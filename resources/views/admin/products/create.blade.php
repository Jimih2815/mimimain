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
        <input name="slug" class="form-control" value="{{ old('slug') }}" placeholder="Để trống sẽ tự tạo">
      </div>
      <div class="mb-3 ten-va-link-con">
        <label class="form-label">Base Price</label>
        <input name="base_price" type="text" inputmode="decimal" class="form-control" value="{{ old('base_price') }}" required>
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

    {{-- Ảnh chính (chọn là upload luôn + preview) --}}
    <div class="mb-3">
      <label class="form-label">Ảnh chính</label>
      <input type="file" id="img_input" name="img" class="form-control" accept="image/*">
      <input type="hidden" name="img_existing" id="img_existing" value="{{ old('img_existing') }}">
      <div class="mt-2">
        <img id="img_preview" style="display:none;width:150px;object-fit:cover;" alt="Main preview">
      </div>
      <small class="text-muted d-block mt-1">Chọn ảnh xong hệ thống sẽ tự upload và hiện preview.</small>
    </div>

    {{-- Ảnh phụ (chọn là upload luôn + preview, có thể xoá) --}}
    <div class="mb-3">
      <label class="form-label">Ảnh phụ (có thể chọn nhiều)</label>
      <input type="file" id="sub_input" name="sub_img[]" class="form-control" accept="image/*" multiple>
      <div id="sub_previews" class="mt-2 d-flex flex-wrap gap-2"></div>
      <small class="text-muted d-block mt-1">Bạn có thể bấm dấu × để bỏ ảnh phụ trước khi lưu.</small>
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

  {{-- Modal báo lỗi (AJAX) --}}
  <div class="modal fade" id="productFormErrorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Không lưu được</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="productFormErrorText" class="mb-2">Có lỗi xảy ra, kiểm tra lại giúp mình nhé.</div>
          <ul id="productFormErrorList" class="mb-0"></ul>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">OK</button>
        </div>
      </div>
    </div>
  </div>

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
               type="text" inputmode="decimal" class="form-control" required>
      </div>

      <div class="me-2" style="width:120px">
        <label class="form-label">Price</label>
        <input type="text" inputmode="decimal"
               class="form-control option-price-helper"
               placeholder="(không bắt buộc)">
      </div>

      {{-- Preview + hidden existing path --}}
      <div class="me-2 text-center" style="width:100px">
        <div class="img-cont" style="display:none;">
          <img class="opt_preview" src="" alt="Option img" style="width:100%;height:100%;object-fit:cover;border:1px solid #ccc;">
        </div>
        <input type="hidden"
               class="opt_existing"
               name="options[{i}][values][{j}][existing_img]"
               value="">
      </div>

      <div class="me-2" style="width:150px">
        <label class="form-label">Ảnh</label>
        <input name="options[{i}][values][{j}][option_img]"
               type="file" accept="image/*" class="form-control opt_upload">
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

  // Base price helper
  const basePriceInput = form.querySelector('input[name="base_price"]');

  // Parse numbers user may type in VN/EU/US formats:
  //  - "159000"          -> 159000
  //  - "100,5"           -> 100.5
  //  - "1.234,56"        -> 1234.56
  //  - "1,234.56"        -> 1234.56
  function parseLocaleNumber(raw) {
    let s = String(raw ?? '').trim();
    if (!s) return NaN;

    // remove spaces + currency symbols
    s = s.replace(/\s+/g, '').replace(/[^0-9,\.\-]/g, '');

    const lastDot = s.lastIndexOf('.');
    const lastComma = s.lastIndexOf(',');

    if (lastDot > -1 && lastComma > -1) {
      // pick the last one as decimal separator
      if (lastComma > lastDot) {
        // 1.234,56 => 1234.56
        s = s.replace(/\./g, '').replace(',', '.');
      } else {
        // 1,234.56 => 1234.56
        s = s.replace(/,/g, '');
      }
    } else if (lastComma > -1) {
      // 100,5 => 100.5
      s = s.replace(',', '.');
    }
    const n = parseFloat(s);
    return Number.isFinite(n) ? n : NaN;
  }

  function toBackendNumberString(raw) {
    const n = parseLocaleNumber(raw);
    return Number.isFinite(n) ? String(n) : '';
  }

  function getBasePrice() {
    return parseLocaleNumber(basePriceInput?.value ?? '');
  }

  function round2(n) {
    return Math.round(n * 100) / 100;
  }
  function calcExtraFromPrice(valueBlock, opts = {}) {
    if (!valueBlock) return;
    const extraInp = valueBlock.querySelector('input[name*="[extra_price]"]');
    const priceInp = valueBlock.querySelector('.option-price-helper');
    if (!extraInp || !priceInp) return;

    const rawPrice = String(priceInp.value ?? '').trim();

    // Nếu user đang gõ dang dở kiểu "100," hoặc "100." thì khoan tính
    if (!opts.force && /[\.,\-]$/.test(rawPrice)) return;

    const price = parseLocaleNumber(rawPrice);
    const base  = getBasePrice();
    if (!Number.isFinite(price) || !Number.isFinite(base)) return;

    // Chỉ auto-fill nếu Extra Price trống hoặc trước đó do auto-fill tạo ra
    const isEmpty = (extraInp.value || '').trim() === '';
    const isAuto  = extraInp.dataset.autofilled === '1';
    if (!opts.force && !(isEmpty || isAuto)) return;

    let extra = round2(price - base);
    if (Object.is(extra, -0)) extra = 0;

    extraInp.value = String(extra);
    extraInp.dataset.autofilled = '1';

    // Trigger để auto-add dòng mới nếu đủ điều kiện
    extraInp.dispatchEvent(new Event('input', { bubbles: true }));
  }


  // Modal lỗi
  const errModalEl  = document.getElementById('productFormErrorModal');
  const errTextEl   = document.getElementById('productFormErrorText');
  const errListEl   = document.getElementById('productFormErrorList');
  const errModal = (window.bootstrap && errModalEl)
    ? new bootstrap.Modal(errModalEl)
    : null;

  function showErrorModal(message, items = []) {
    if (!errTextEl || !errListEl) return alert(message || 'Có lỗi xảy ra');

    errTextEl.textContent = message || 'Có lỗi xảy ra, kiểm tra lại giúp mình nhé.';
    errListEl.innerHTML = '';
    (items || []).forEach(t => {
      const li = document.createElement('li');
      li.textContent = t;
      errListEl.appendChild(li);
    });

    if (errModal) errModal.show();
    else alert([message, ...(items||[])].filter(Boolean).join('\n'));
  }

  function errorKeyToName(key) {
    if (!key) return key;
    // Laravel validation errors trả về dạng dot: options.0.values.0.value
    const parts = String(key).split('.');
    if (parts.length === 1) return key;
    return parts[0] + parts.slice(1).map(p => `[${p}]`).join('');
  }

  // Track upload đang chạy để tránh bấm Lưu khi ảnh chưa upload xong
  let pendingUploads = 0;
  function setSubmitting(isSubmitting) {
    const btn = form.querySelector('button[type="submit"], button:not([type]), .nut-cap-nhat');
    if (!btn) return;
    btn.disabled = isSubmitting;
    btn.dataset.originalText = btn.dataset.originalText || btn.textContent;
    btn.textContent = isSubmitting ? 'Đang lưu…' : btn.dataset.originalText;
  }
  // ===== Upload ảnh ngay khi chọn (Main/Sub/Option) =====
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  const uploadUrl = "{{ route('admin.products.uploadImage') }}";

  async function uploadOne(file, folder) {
    pendingUploads++;
    const fd = new FormData();
    fd.append('file', file);
    fd.append('folder', folder);

    const res = await fetch(uploadUrl, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrf },
      body: fd
    });

    if (!res.ok) throw new Error('Upload failed');
    return await res.json(); // {location, path}
  }

  async function safeUploadOne(file, folder) {
    try {
      return await uploadOne(file, folder);
    } finally {
      pendingUploads = Math.max(0, pendingUploads - 1);
    }
  }

  // Ảnh chính
  const imgInput = document.getElementById('img_input');
  const imgExisting = document.getElementById('img_existing');
  const imgPreview = document.getElementById('img_preview');

  if (imgInput && imgExisting && imgPreview) {
    imgInput.addEventListener('change', async () => {
      const file = imgInput.files?.[0];
      if (!file) return;

      try {
        const out = await safeUploadOne(file, 'products/main');
        imgExisting.value = out.path;
        imgPreview.src = out.location;
        imgPreview.style.display = 'block';
        imgInput.value = '';
      } catch (e) {
        alert('Upload ảnh chính lỗi, thử lại nhé!');
        console.error(e);
      }
    });
  }

  // Ảnh phụ
  const subInput = document.getElementById('sub_input');
  const subWrap = document.getElementById('sub_previews');

  function addSubThumb(location, path) {
    if (!subWrap) return;
    const box = document.createElement('div');
    box.className = 'sub-one position-relative';
    box.dataset.path = path;
    box.style.width = '80px';

    box.innerHTML = `
      <img src="${location}" width="80" height="80" style="object-fit:cover;border:1px solid #ccc;" alt="">
      <button type="button" class="btn btn-sm btn-danger sub-remove"
              style="position:absolute;top:-6px;right:-6px;border-radius:999px;line-height:1;">×</button>
      <input type="hidden" name="sub_img_existing[]" value="${path}">
    `;

    box.querySelector('.sub-remove')?.addEventListener('click', () => box.remove());
    subWrap.appendChild(box);
  }

  if (subInput && subWrap) {
    subWrap.addEventListener('click', (e) => {
      if (e.target.classList.contains('sub-remove')) {
        e.target.closest('.sub-one')?.remove();
      }
    });

    subInput.addEventListener('change', async () => {
      const files = Array.from(subInput.files || []);
      if (!files.length) return;

      for (const f of files) {
        try {
          const out = await safeUploadOne(f, 'products/sub');
          addSubThumb(out.location, out.path);
        } catch (e) {
          alert('Upload ảnh phụ lỗi, thử lại nhé!');
          console.error(e);
          break;
        }
      }
      subInput.value = '';
    });
  }

  // Option image: input file có class .opt_upload
  document.addEventListener('change', async (e) => {
    const input = e.target;
    if (!input.classList.contains('opt_upload')) return;

    const file = input.files?.[0];
    if (!file) return;

    const row = input.closest('.value-block');
    if (!row) return;

    const existing = row.querySelector('.opt_existing') || row.querySelector('input[name*="[existing_img]"]');
    const cont = row.querySelector('.img-cont');
    const img = row.querySelector('.opt_preview') || cont?.querySelector('img');

    try {
      const out = await safeUploadOne(file, 'products/options');
      if (existing) existing.value = out.path;
      if (img) img.src = out.location;
      if (cont) cont.style.display = 'block';
      input.value = '';
    } catch (e) {
      alert('Upload ảnh option lỗi, thử lại nhé!');
      console.error(e);
    }
  });



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

  // Tính Extra Price tự động khi nhập Price (chỉ là công cụ tính hộ)
  // Debounce để tránh tính khi user mới gõ 1-2 ký tự (vd vừa gõ "1" của 159000)
  const _debounceTimers = new WeakMap();
  function debounce(key, fn, wait = 450) {
    const old = _debounceTimers.get(key);
    if (old) clearTimeout(old);
    const t = setTimeout(fn, wait);
    _debounceTimers.set(key, t);
  }

  // Khi gõ Price: đợi user ngừng gõ 1 nhịp rồi mới tính Extra Price
  optionsContainer.addEventListener('input', (e) => {
    if (e.target?.classList?.contains('option-price-helper')) {
      const vb = e.target.closest('.value-block');
      debounce(e.target, () => calcExtraFromPrice(vb, { force: false }));
    }
  });

  // Khi rời ô Price (blur): tính luôn (nếu user gõ xong rồi click ra ngoài)
  optionsContainer.addEventListener('blur', (e) => {
    if (e.target?.classList?.contains('option-price-helper')) {
      const vb = e.target.closest('.value-block');
      calcExtraFromPrice(vb, { force: true });
    }
  }, true);

  // Nếu user tự sửa Extra Price bằng tay thì đừng auto-override nữa
  optionsContainer.addEventListener('keydown', (e) => {
    if (e.target && e.target.name && e.target.name.includes('[extra_price]')) {
      e.target.dataset.autofilled = '0';
    }
  });

  // Nếu đổi Base Price: chỉ recalc những dòng Extra đang trống / do auto-fill
  basePriceInput?.addEventListener('input', () => {
    debounce(basePriceInput, () => {
      optionsContainer.querySelectorAll('.value-block').forEach(vb => {
        const extraInp = vb.querySelector('input[name*="[extra_price]"]');
        const priceInp = vb.querySelector('.option-price-helper');
        if (!priceInp) return;
        const hasPrice = String(priceInp.value ?? '').trim() !== '';
        const canAuto = !extraInp || (String(extraInp.value ?? '').trim() === '' || extraInp.dataset.autofilled === '1');
        if (hasPrice && canAuto) calcExtraFromPrice(vb, { force: false });
      });
    }, 300);
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

  // Submit bằng AJAX => không reload trang, giữ nguyên text/ảnh đã nhập
  form.addEventListener('submit', async function(e) {
    e.preventDefault();

    // 0) Nếu đang upload ảnh mà đã bấm Lưu => chặn lại
    if (pendingUploads > 0) {
      showErrorModal('Ảnh đang upload', ['Đợi ảnh upload xong rồi bấm Lưu lại nha (đỡ bị mất công).']);
      return;
    }

    // 1) Phải có ít nhất 1 option
    if (optionsContainer.querySelectorAll('.option-block').length === 0) {
      optionError.style.display = 'block';
      showErrorModal('Thiếu phân loại', ['Thêm ít nhất 1 option/phân loại trước khi lưu.']);
      return;
    }
    optionError.style.display = 'none';

    // 2) Loại bỏ dòng value hoàn toàn trống (dòng auto-add)
    document.querySelectorAll('.value-block').forEach(vb => {
      const valInp   = vb.querySelector('input[name*="[value]"]');
      const extraInp = vb.querySelector('input[name*="[extra_price]"]');
      if ((valInp?.value || '').trim() === '' && (extraInp?.value || '').trim() === '') {
        vb.remove();
      }
    });

    // 2.5) Chuẩn hoá số (cho phép user nhập 100,5 / 1.234,56 ...)
    //      -> backend luôn nhận dạng chuẩn dùng dấu chấm.
    //      (price helper không submit nên không cần)
    const numericInputs = [
      basePriceInput,
      ...form.querySelectorAll('input[name*="[extra_price]"]'),
    ].filter(Boolean);

    numericInputs.forEach(inp => {
      const raw = String(inp.value ?? '').trim();
      if (!raw) return;
      const norm = toBackendNumberString(raw);
      if (norm !== '') inp.value = norm;
    });

    // 3) Đồng bộ TinyMCE -> textarea trước khi lấy FormData
    if (window.tinymce) {
      try { tinymce.triggerSave(); } catch (err) {}
    }

    // 4) Clear trạng thái invalid cũ
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

    // 5) Gửi request
    setSubmitting(true);
    try {
      const fd = new FormData(form);
      const res = await fetch(form.action, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: fd
      });

      if (res.ok) {
        const out = await res.json().catch(() => ({}));
        // Redirect về danh sách sản phẩm
        if (out.redirect) {
          window.location.href = out.redirect;
          return;
        }
        // fallback
        window.location.reload();
        return;
      }

      // Validation error
      if (res.status === 422) {
        const data = await res.json().catch(() => ({}));
        const errors = data.errors || {};
        const flat = [];
        Object.values(errors).forEach(arr => {
          (arr || []).forEach(msg => flat.push(msg));
        });

        // mark invalid fields
        Object.keys(errors).forEach(key => {
          const k1 = key;
          const k2 = errorKeyToName(key);
          const q1 = `[name="${CSS.escape(k1)}"]`;
          const q2 = `[name="${CSS.escape(k2)}"]`;
          const el = form.querySelector(q1) || form.querySelector(q2) || form.querySelector(`${q1}[]`) || form.querySelector(`${q2}[]`);
          if (el) el.classList.add('is-invalid');
        });

        showErrorModal('Chưa lưu được vì còn thiếu/nhập sai vài chỗ', flat.length ? flat : ['Kiểm tra lại các trường bắt buộc.']);
        return;
      }

      // Other errors
      const text = await res.text().catch(() => '');
      console.error('Store error:', res.status, text);
      showErrorModal('Lỗi hệ thống', ['Server trả về lỗi, thử lại sau hoặc báo mình đoạn lỗi (Console/Network) để bắt bệnh nhanh.']);
    } catch (err) {
      console.error(err);
      showErrorModal('Mất kết nối', ['Không gửi được dữ liệu lên server. Kiểm tra mạng rồi thử lại nha.']);
    } finally {
      setSubmitting(false);
    }
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
