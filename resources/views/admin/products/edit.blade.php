@extends('layouts.admin')

@section('content')
<div class="container">
  <h1 class="mb-4">Sửa sản phẩm #{{ $product->id }}</h1>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.products.update', $product) }}"
        method="POST"
        enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- Thông tin cơ bản --}}
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input name="name"
             class="form-control"
             value="{{ old('name', $product->name) }}"
             required>
    </div>

    <div class="mb-3">
      <label class="form-label">Slug</label>
      <input name="slug"
             class="form-control"
             value="{{ old('slug', $product->slug) }}"
             required>
    </div>

    <div class="mb-3">
      <label class="form-label">Description</label>
      <textarea name="description"
                class="form-control"
                rows="3">{{ old('description', $product->description) }}</textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Base Price</label>
      <input name="base_price"
             type="number"
             class="form-control"
             value="{{ old('base_price', $product->base_price) }}"
             required>
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
    <h4>Options</h4>
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
            <label class="form-label">Tên Option</label>
            <input name="options[{{ $i }}][name]"
                   class="form-control"
                   value="{{ old("options.$i.name", $vals->first()->type->name) }}"
                   required>
          </div>

          <div class="values-container">
            @foreach($vals as $j => $val)
              <div class="d-flex align-items-end mb-2 value-block" data-val-index="{{ $j }}">
                <div class="me-2 flex-fill">
                  <label class="form-label">Giá trị</label>
                  <input name="options[{{ $i }}][values][{{ $j }}][value]"
                         class="form-control"
                         value="{{ old("options.$i.values.$j.value", $val->value) }}"
                         required>
                </div>
                <div class="me-2" style="width:120px">
                  <label class="form-label">Extra Price</label>
                  <input name="options[{{ $i }}][values][{{ $j }}][extra_price]"
                         type="number" step="0.01"
                         class="form-control"
                         value="{{ old("options.$i.values.$j.extra_price", $val->extra_price) }}"
                         required>
                </div>
                <button type="button" class="btn btn-sm btn-danger remove-value">×</button>
              </div>
            @endforeach
          </div>

          <button type="button"
                  class="btn btn-sm btn-info add-value"
                  data-opt-index="{{ $i }}">
            + Thêm Giá trị
          </button>
        </div>
      @endforeach
    </div>
    <button type="button"
            id="add-option-btn"
            class="btn btn-success btn-sm mb-4">
      + Thêm Option
    </button>

    <button class="btn btn-primary">Cập nhật</button>
  </form>

  {{-- Templates ẩn --}}
  <template id="tpl-option">
    <div class="card mb-3 option-block p-3" data-index="{i}">
      <div class="d-flex justify-content-between align-items-center">
        <h5>Option #{i_display}</h5>
        <button type="button" class="btn btn-sm btn-danger remove-option">–</button>
      </div>
      <div class="mb-2">
        <label class="form-label">Tên Option</label>
        <input name="options[{i}][name]" class="form-control" required>
      </div>
      <div class="values-container"></div>
      <button type="button"
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
        <input name="options[{i}][values][{j}][value]"
               class="form-control" required>
      </div>
      <div class="me-2" style="width:120px">
        <label class="form-label">Extra Price</label>
        <input name="options[{i}][values][{j}][extra_price]"
               type="number" step="0.01"
               class="form-control" required>
      </div>
      <button type="button" class="btn btn-sm btn-danger remove-value">×</button>
    </div>
  </template>
</div>
@endsection

@push('scripts')
<script>
  let optCount = document.querySelectorAll('.option-block').length;

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

  document.getElementById('options-container')
    .addEventListener('click', e => {
      if (e.target.matches('.add-value')) {
        addValue(e.target.getAttribute('data-opt-index'));
      }
      if (e.target.matches('.remove-option')) {
        e.target.closest('.option-block').remove();
      }
      if (e.target.matches('.remove-value')) {
        e.target.closest('.value-block').remove();
      }
    });

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
</script>
@endpush
