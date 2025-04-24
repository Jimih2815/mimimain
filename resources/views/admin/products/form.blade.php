{{-- Dùng chung cho create & edit --}}
@php
    // nếu edit thì có sẵn $product
    $isEdit = isset($product);
@endphp

<form method="POST"
      action="{{ $isEdit
                ? route('admin.products.update', $product)
                : route('admin.products.store') }}"
      enctype="multipart/form-data">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    {{-- Tên, slug, mô tả, giá cơ bản --}}
    <div class="mb-3">
        <label for="name" class="form-label">Tên sản phẩm</label>
        <input type="text" name="name" id="name"
               class="form-control"
               value="{{ old('name', $product->name ?? '') }}" required>
    </div>

    <div class="mb-3">
        <label for="slug" class="form-label">Slug</label>
        <input type="text" name="slug" id="slug"
               class="form-control"
               value="{{ old('slug', $product->slug ?? '') }}" required>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Mô tả</label>
        <textarea name="description" id="description"
                  class="form-control"
                  rows="3">{{ old('description', $product->description ?? '') }}</textarea>
    </div>

    <div class="mb-3">
        <label for="base_price" class="form-label">Giá gốc</label>
        <input type="number" name="base_price" id="base_price"
               class="form-control"
               value="{{ old('base_price', $product->base_price ?? '') }}" required>
    </div>

    {{-- Ảnh chính --}}
    <div class="mb-3">
        <label for="img" class="form-label">Ảnh chính</label>
        <input type="file" name="img" id="img" class="form-control"
               {{ $isEdit ? '' : 'required' }}>
        @if($isEdit && $product->img)
            <div class="mt-2">
                <img src="{{ asset('storage/'.$product->img) }}"
                     width="150" style="object-fit:cover;" alt="Ảnh chính">
            </div>
        @endif
    </div>

    {{-- Ảnh phụ --}}
    <div class="mb-3">
        <label for="sub_img" class="form-label">Ảnh phụ (multiple)</label>
        <input type="file" name="sub_img[]" id="sub_img"
               class="form-control" multiple>
        @if($isEdit && !empty($product->sub_img))
            <div class="mt-2 d-flex flex-wrap gap-2">
                @foreach($product->sub_img as $sub)
                    <img src="{{ asset('storage/'.$sub) }}"
                         width="80" style="object-fit:cover;"
                         class="border rounded" alt="sub-img">
                @endforeach
            </div>
        @endif
    </div>

    {{-- Options (giữ nguyên form của bạn hoặc build dynamic JS) --}}
    {{-- ... --}}

    <button type="submit" class="btn btn-success">
        {{ $isEdit ? 'Cập nhật' : 'Tạo mới' }}
    </button>
</form>
