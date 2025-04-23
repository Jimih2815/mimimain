{{-- resources/views/admin/products/edit.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container">
  <h1 class="mb-4">Sửa sản phẩm #{{ $product->id }}</h1>

  <form action="{{ route('admin.products.update', $product) }}" method="POST">
    @csrf
    @method('PUT')

    {{-- Thông tin cơ bản --}}
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input 
        name="name" 
        class="form-control" 
        value="{{ old('name', $product->name) }}" 
        required>
    </div>

    <div class="mb-3">
      <label class="form-label">Slug</label>
      <input 
        name="slug" 
        class="form-control" 
        value="{{ old('slug', $product->slug) }}" 
        required>
    </div>

    <div class="mb-3">
      <label class="form-label">Description</label>
      <textarea 
        name="description" 
        class="form-control"
      >{{ old('description', $product->description) }}</textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Base Price</label>
      <input 
        name="base_price" 
        type="number" 
        class="form-control" 
        value="{{ old('base_price', $product->base_price) }}" 
        required>
    </div>

    <hr>
    <h4>Options</h4>

    <div id="options-container">
      @php
        // Nhóm OptionValue theo option_type_id
        $groups = $product->optionValues->groupBy(fn($v) => $v->type->id);
      @endphp

      @foreach($groups as $typeId => $vals)
        @php $i = $loop->index; @endphp
        <div class="card mb-3 option-block p-3" data-index="{{ $i }}">
          <div class="d-flex justify-content-between">
            <h5>Option #{{ $i + 1 }}</h5>
            <button type="button" class="btn btn-sm btn-danger remove-option">–</button>
          </div>

          <div class="mb-2">
            <label class="form-label">Tên Option</label>
            <input 
              name="options[{{ $i }}][name]" 
              class="form-control" 
              value="{{ old("options.$i.name", $vals->first()->type->name) }}" 
              required>
          </div>

          <div class="values-container">
            @foreach($vals as $j => $val)
              <div class="d-flex align-items-end mb-2 value-block" data-val-index="{{ $j }}">
                <div class="me-2 flex-fill">
                  <label class="form-label">Giá trị</label>
                  <input
                    name="options[{{ $i }}][values][{{ $j }}][value]"
                    class="form-control"
                    value="{{ old("options.$i.values.$j.value", $val->value) }}"
                    required>
                </div>
                <div class="me-2" style="width:120px">
                  <label class="form-label">Extra Price</label>
                  <input
                    name="options[{{ $i }}][values][{{ $j }}][extra_price]"
                    type="number" step="0.01"
                    class="form-control"
                    value="{{ old("options.$i.values.$j.extra_price", $val->extra_price) }}"
                    required>
                </div>
                <button type="button" class="btn btn-sm btn-danger remove-value">×</button>
              </div>
            @endforeach
          </div>

          <button 
            type="button" 
            class="btn btn-sm btn-info add-value"
            data-opt-index="{{ $i }}">
            + Thêm Giá trị
          </button>
        </div>
      @endforeach
    </div>

    <button 
      type="button" 
      id="add-option-btn" 
      class="btn btn-success btn-sm mb-4">
      + Thêm Option
    </button>

    <button class="btn btn-primary">Cập nhật</button>
  </form>

  {{-- ——— Templates ẩn ngay trong file để JS dùng ——— --}}
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
      <button 
        type="button" 
        class="btn btn-sm btn-info add-value" 
        data-opt-index="{i}">
        + Thêm Giá trị
      </button>
    </div>
  </template>

  <template id="tpl-value">
    <div class="d-flex align-items-end mb-2 value-block">
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
          type="number" step="0.01" 
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
  let optCount = document.querySelectorAll('.option-block').length;

  // Thêm block Option mới
  document.getElementById('add-option-btn').onclick = () => {
    const tpl = document.getElementById('tpl-option').innerHTML;
    const html = tpl
      .replaceAll('{i}', optCount)
      .replaceAll('{i_display}', optCount + 1);
    const wrapper = document.createElement('div');
    wrapper.innerHTML = html;
    document.getElementById('options-container').appendChild(wrapper);
    addValue(optCount);
    optCount++;
  };

  // Delegate sự kiện trong container
  document.getElementById('options-container')
    .addEventListener('click', e => {
      // Thêm Value
      if (e.target.matches('.add-value')) {
        const idx = e.target.getAttribute('data-opt-index');
        addValue(idx);
      }
      // Xoá Option
      if (e.target.matches('.remove-option')) {
        e.target.closest('.option-block').remove();
      }
      // Xoá Value
      if (e.target.matches('.remove-value')) {
        e.target.closest('.value-block').remove();
      }
    });

  // Hàm thêm Value vào option thứ i
  function addValue(i) {
    const block = document.querySelector(`.option-block[data-index="${i}"]`);
    const container = block.querySelector('.values-container');
    const j = container.querySelectorAll('.value-block').length;
    let tpl = document.getElementById('tpl-value').innerHTML;
    tpl = tpl
      .replaceAll('{i}', i)
      .replaceAll('{j}', j);
    const wrapper = document.createElement('div');
    wrapper.innerHTML = tpl;
    container.appendChild(wrapper);
  }
</script>
@endpush
