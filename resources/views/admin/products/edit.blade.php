@extends('layouts.admin')

@section('content')
<div class="sua-chi-tiet-san-pham">
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

  <form action="{{ route('admin.products.update', $product) }}"
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
