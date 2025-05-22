{{-- resources/views/admin/sectors/form.blade.php --}}
@php
    // Xác định xem đang tạo mới hay chỉnh sửa
    $isEdit = ! empty($sector->id);
    // Thiết lập route tương ứng
    $action = $isEdit
        ? route('admin.sectors.update', $sector->id)
        : route('admin.sectors.store');

    // Chuẩn bị mảng collections đã lưu, sắp xếp theo sort_order
    $defaultCols = collect(
        $sector->collections->map(fn($c) => [
            'id'           => $c->id,
            'name'         => $c->name,
            'custom_name'  => $c->pivot->custom_name,
            'custom_image' => $c->pivot->custom_image,
            'sort_order'   => $c->pivot->sort_order,
        ])->toArray()
    )
    ->sortBy('sort_order')
    ->values()
    ->toArray();

    // Nếu có old (validation fail) thì dùng old, không thì dùng defaultCols
    $collectionsData = old('collections') ? old('collections') : $defaultCols;
@endphp

@extends('layouts.admin')

@section('content')
    <a href="{{ route('admin.sectors.index') }}"
       class="nut-quay-ve ms-2 text-decoration-none mb-5">
        <i class="fa-solid fa-chevron-left"></i> Quay lại danh sách
    </a>

    <h1 class="mb-4">{{ $isEdit ? 'Chỉnh sửa' : 'Tạo mới' }} Sector</h1>

    <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        {{-- Tên Sector --}}
        <div class="mb-3">
            <label for="name" class="form-label chu-dam">Tên Sector</label>
            <input type="text"
                   name="name"
                   id="name"
                   class="form-control"
                   value="{{ old('name', $sector->name) }}"
                   required>
        </div>

        {{-- Slug --}}
        <div class="mb-3">
            <label for="slug" class="form-label chu-dam">Tạo Link</label>
            <input type="text"
                   name="slug"
                   id="slug"
                   class="form-control"
                   value="{{ old('slug', $sector->slug) }}"
                   required>
        </div>

        {{-- Ảnh Sector --}}
        <div class="mb-3">
            <label class="form-label chu-dam">Ảnh Sector</label>
            <div class="d-flex justify-content-center align-items-center">
                @if($isEdit && $sector->image)
                    <div><img src="{{ asset('storage/' . $sector->image) }}" width="150"></div>
                @endif
                <input type="file" style="height: 100%;" name="image" class="form-control ms-3">
            </div>
        </div>

        {{-- Các Collection --}}
        <div class="mb-3">
            <label class="form-label chu-dam">Các Collection</label>
            <table class="table text-center" id="selected-collections-table">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th style="width: 12rem;">Collection</th>
                        <th>Tên hiển thị</th>
                        <th>Ảnh hiển thị</th>
                        <th style="width:6rem;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($collectionsData as $index => $col)
                        <tr>
                            {{-- STT + sort_order --}}
                            <td>
                                <span class="sort-order-display">
                                    {{ old("collections.$index.sort_order", $col['sort_order']) }}
                                </span>
                                <input type="hidden"
                                       name="collections[{{ $index }}][sort_order]"
                                       value="{{ old("collections.$index.sort_order", $col['sort_order']) }}">
                            </td>

                            {{-- Collection dropdown có tìm kiếm --}}
                            <td>
                                <select name="collections[{{ $index }}][collection_id]"
                                        class="form-select form-select-sm select-collection"
                                        required>
                                    <option value="">-- Chọn collection --</option>
                                    @foreach($collections as $id => $name)
                                        <option value="{{ $id }}"
                                            {{ $id == ($col['collection_id'] ?? $col['id']) ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            {{-- Tên hiển thị tuỳ chỉnh --}}
                            <td>
                                <input type="text"
                                       name="collections[{{ $index }}][custom_name]"
                                       class="form-control"
                                       value="{{ old("collections.$index.custom_name", $col['custom_name']) }}">
                            </td>

                            {{-- Ảnh hiển thị --}}
                            <td class="d-flex justify-content-center align-items-center">
                                @if(! empty($col['custom_image']))
                                    <img src="{{ asset('storage/' . $col['custom_image']) }}" width="80">
                                @endif
                                <input type="file"
                                       name="collections[{{ $index }}][custom_image]"
                                       style="width:4.5rem;"
                                       class="form-control form-control-sm ms-2">
                            </td>

                            {{-- Xoá --}}
                            <td>
                                <button type="button"
                                        class="btn-mimi nut-xoa remove-collection">
                                    Xóa
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <button type="button" id="add-collection-btn" class="btn-mimi nut-xanh-la">
                + Collection
            </button>
        </div>

        <button type="submit" class="btn-mimi nut-xanh mt-5 mb-5">
            {{ $isEdit ? 'Cập nhật' : 'Tạo mới' }}
        </button>
        <a href="{{ route('admin.sectors.index') }}" class="btn-mimi nut-do ms-2 text-decoration-none">Hủy</a>
    </form>
@endsection


{{-- ====================== CSS ====================== --}}
@push('styles')
    <!-- Tom Select -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.4.2/dist/css/tom-select.css">

    <style>
        /* Giữ nguyên CSS cũ */
        #selected-collections-table tbody tr { cursor: move; }

        .container-fluid { width: 60%; }

        /* Căn giữa nội dung ô theo chiều ngang + dọc */
        #selected-collections-table td,
        #selected-collections-table th {
            vertical-align: middle;
            text-align: center;
        }

        .chu-dam {
            font-size: 1.5rem;
            font-weight: 600;
            margin-top: 2rem;
        }

        .nut-quay-ve {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 0.5rem;
            color: #b18623;
            font-size: 1.2rem;
            font-weight: 600;
        }
        .ts-wrapper {
            position: relative;
            padding: 0 !important;
        }
    </style>
@endpush


{{-- ====================== JS ====================== --}}
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.2/dist/js/tom-select.complete.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ==== 0. Helper: kích hoạt Tom Select cho một <select> =====
            const activateTomSelect = (el) => {
                if (el.tomselect) return;               // tránh khởi tạo lại
                new TomSelect(el, {
                    placeholder: '-- Chọn collection --',
                    create: false,
                    duplicate: false,
                    allowEmptyOption: true
                });
            };

            // ==== 1. Các collection server gửi sang JS =====
            const allCollections = @json($collections);

            let counter   = Date.now();
            const tableBody = document.querySelector('#selected-collections-table tbody');

            // ==== 2. Khởi tạo Tom Select cho các select đã có sẵn ====
            document.querySelectorAll('.select-collection').forEach(activateTomSelect);

            // ==== 3. Thêm dòng mới ====
            document.getElementById('add-collection-btn').addEventListener('click', function () {
                counter++;
                const index = 'new_' + counter;

                // render option list
                let options = '<option value="">-- Chọn collection --</option>';
                Object.entries(allCollections).forEach(([id, name]) => {
                    options += `<option value="${id}">${name}</option>`;
                });

                // tạo tr
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <span class="sort-order-display">0</span>
                        <input type="hidden"
                               name="collections[${index}][sort_order]"
                               value="0">
                    </td>
                    <td>
                        <select name="collections[${index}][collection_id]"
                                class="form-select form-select-sm select-collection"
                                required>
                            ${options}
                        </select>
                    </td>
                    <td>
                        <input type="text"
                               name="collections[${index}][custom_name]"
                               class="form-control form-control-sm">
                    </td>
                    <td>
                        <input type="file"
                               name="collections[${index}][custom_image]"
                               class="form-control form-control-sm">
                    </td>
                    <td>
                        <button type="button"
                                class="btn-mimi nut-xoa remove-collection">
                            Xóa
                        </button>
                    </td>
                `;

                tableBody.appendChild(row);

                // Kích hoạt Tom Select cho select vừa thêm
                activateTomSelect(row.querySelector('.select-collection'));

                updateSortOrders();
            });

            // ==== 4. Xoá dòng ====
            tableBody.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-collection')) {
                    e.target.closest('tr').remove();
                    updateSortOrders();
                }
            });

            // ==== 5. Kéo-thả sắp xếp ====
            Sortable.create(tableBody, {
                animation: 150,
                onEnd: updateSortOrders
            });

            // ==== 6. Cập nhật STT và hidden sort_order ====
            function updateSortOrders() {
                Array.from(tableBody.children).forEach((row, idx) => {
                    const input = row.querySelector('input[name$="[sort_order]"]');
                    if (input) input.value = idx;
                    const span = row.querySelector('.sort-order-display');
                    if (span) span.textContent = idx;
                });
            }

            updateSortOrders(); // chạy lần đầu
        });
    </script>
@endpush
