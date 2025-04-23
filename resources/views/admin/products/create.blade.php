{{-- resources/views/admin/products/create.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container">
  <h1 class="mb-4">Thêm sản phẩm mới</h1>

  <form action="{{ route('admin.products.store') }}" method="POST">
    @csrf

    {{-- Thông tin cơ bản --}}
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input name="name" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Slug</label>
      <input name="slug" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Description</label>
      <textarea name="description" class="form-control"></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Base Price</label>
      <input name="base_price" type="number" class="form-control" required>
    </div>

    <hr>
    <h4>Options</h4>
    <div id="options-container"></div>
    <button type="button" id="add-option-btn" class="btn btn-success btn-sm mb-4">
      + Thêm Option
    </button>

    <button class="btn btn-primary">Lưu lại</button>
  </form>

  {{-- Template ẩn cho OptionType --}}
  <template id="tpl-option">
    <div class="card mb-3 option-block p-3" data-index="{i}">
      <div class="d-flex justify-content-between">
        <h5>Option #{i_display}</h5>
        <button type="button" class="btn btn-sm btn-danger remove-option">–</button>
      </div>
      <div class="mb-2">
        <label class="form-label">Tên Option</label>
        <input name="options[{i}][name]" class="form-control" required>
      </div>
      <div class="values-container"></div>
      <button type="button" class="btn btn-sm btn-info add-value" data-opt-index="{i}">
        + Thêm Giá trị
      </button>
    </div>
  </template>

  {{-- Template ẩn cho OptionValue --}}
  <template id="tpl-value">
    <div class="d-flex align-items-end mb-2 value-block" data-val-index="{j}">
      <div class="me-2 flex-fill">
        <label class="form-label">Giá trị</label>
        <input
          name="options[{i}][values][{j}][value]"
          class="form-control"
          required>
      </div>
      <div class="me-2" style="width:120px">
        <label class="form-label">Extra Price</label>
        <input
          name="options[{i}][values][{j}][extra_price]"
          type="number"
          step="0.01"
          class="form-control"
          required>
      </div>
      <button type="button" class="btn btn-sm btn-danger remove-value">×</button>
    </div>
  </template>
</div>
@endsection

@push('scripts')
<script>
  let optCount = 0;

  // Tạo option mới
  document.getElementById('add-option-btn').onclick = () => {
    const tpl = document.getElementById('tpl-option').innerHTML;
    const html = tpl
      .replaceAll('{i}', optCount)
      .replaceAll('{i_display}', optCount + 1);
    const div = document.createElement('div');
    div.innerHTML = html;
    document.getElementById('options-container').appendChild(div);
    addValue(optCount);
    optCount++;
  };

  // Delegate cho các nút trong options-container
  document.getElementById('options-container')
    .addEventListener('click', e => {
      // Thêm value
      if (e.target.matches('.add-value')) {
        addValue(e.target.dataset.optIndex);
      }
      // Xoá option
      if (e.target.matches('.remove-option')) {
        e.target.closest('.option-block').remove();
      }
      // Xoá value
      if (e.target.matches('.remove-value')) {
        e.target.closest('.value-block').remove();
      }
    });

  // Hàm thêm value cho option thứ i
  function addValue(i) {
    const block = document.querySelector(`.option-block[data-index="${i}"]`);
    const container = block.querySelector('.values-container');
    const j = container.querySelectorAll('.value-block').length;
    const tpl = document.getElementById('tpl-value').innerHTML;
    const html = tpl
      .replaceAll('{i}', i)
      .replaceAll('{j}', j);
    const div = document.createElement('div');
    div.innerHTML = html;
    container.appendChild(div);
  }
</script>
@endpush
