<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Đơn thủ công</title>
  <link rel="stylesheet" href="/logoday.css">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <!-- Bootstrap Datepicker CSS -->
  <link 
    rel="stylesheet" 
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css" 
    integrity="sha512-m5aUQh3mh1+1Q+F4DvMKnT9WYkbOjFAn9xELnN6bR4A0lXkOBN6H+y7p8oy5cCXPS9h0uWz/m5bL6h+XrUYzUg==" 
    crossorigin="anonymous" />



  <style>
  /* ========== RESET & BIẾN ========== */
  :root {
    --radius: 8px;
    --shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
    --fs-body: 16px;
    --bg-light: #f8f9fa;
    --border-color: #ced4da;
  }
  * {
    box-sizing: border-box;
    margin: 0;
  }
  body {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: var(--fs-body);
    line-height: 1.5;
    padding: 1rem;
    /* Cho phép cuộn ngang nếu cần (tùy chỉnh trong container cụ thể) */
  }

  /* ========== HEADER & BUTTON ========== */
  h1 {
    font-size: 1.8rem;
    margin-bottom: 1rem;
  }
  .btn {
    font-size: 0.9rem;
    background-color: #d1a029;
    border: 1px solid #d1a029;
  }
 .btn:hover {
  background-color: #b18623;
  border-color: #d1a029;
 }
 .page-link {
  color: white;
  background-color: #d1a029; 
  border-color: #d1a029;
 }
 .page-link:hover {
  background-color: #b18623;
 }
 .active>.page-link, .page-link.active {
  background-color: #d1a029; 
    border-color: #d1a029;
 }

  /* ========== SKU INPUT ROW ========== */
  .sku-row {
    position: relative;
  }
  .sku-row .form-control {
    font-size: 0.9rem;
    padding: 0.35rem 0.5rem;
  }
  .sku-results {
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    width: 100%;
    max-height: 200px;
    overflow-y: auto;
    background: #fff;
    border: 1px solid var(--border-color);
    border-top: none;
    border-radius: 0 0 var(--radius) var(--radius);
  }
  .sku-input.filled,
  .sku-qty.filled {
    background: var(--bg-light);
  }

  /* ========== TABLE CHÍNH ========== */
  .table {
    
    background: #fff;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    width: 100%;
    margin-bottom: 1rem;
  }
  .table th,
  .table td {
    vertical-align: middle;
    text-align: center;
    padding: 0.75rem;
  }

  /* ========== CONTAINER ĐỂ CUỘN HORIZONTALLY ========== */
  .table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  /* ========== BẢNG SKU CON ========== */
  .sku-table {
    width: 100%;
    table-layout: fixed;
  }
  .sku-table td:first-child {
  width: 45%;
  word-break: break-word;
  text-align: left;
}
.sku-table td:last-child {
  width: 55%;
  white-space: nowrap;
  text-align: right;
}


  /* ========== MODAL ẢNH ========== */
  .modal-fullscreen .modal-content {
    background: rgba(0, 0, 0, 0.85);
    border: none;
  }
  .modal-body img {
    max-width: 100%;
    max-height: 100vh;
    object-fit: contain;
  }
.nav-link {
  color: #4ab3af;
}
.green-btn {
  background-color: #3f9426;
}
.red-btn {
  background-color: #b83232;
}
  /* ========== MEDIA QUERIES ========== */

  /* Cho máy tính bảng và kích thước màn hình nhỏ (≤ 991.98px) */
  @media (min-width: 576px) and (max-width: 991.98px) {
    /* Nếu bảng có quá nhiều cột, đặt chiều rộng tối thiểu cho bảng */
    .table-responsive .table-nay {
      min-width: 2000px;
    }
    .table-responsive .table {
      /* min-width: 250px; */
    }
    .text-start-text-end{
      display: flex;
    flex-direction: row;
    width: 200%;
    }
    .text-start-text-end .text-start {
      width: 500% !important;
    }
    .text-start-text-end .text-end {
      width: 20% !important;
    }
  }

  /* Cho điện thoại (≤ 576px) */
  @media (max-width: 576px) {
    :root {
      --fs-body: 14px;
    }
    .row>* {
      padding-left:0px;
    }
    div.dataTables_wrapper div.dataTables_length, 
    div.dataTables_wrapper div.dataTables_filter {
      text-align: left;
      padding-bottom:0.5rem;
    }
    .table-responsive .table-nay {
     min-width: 2000px !important;
    }
    h1 {
      font-size: 2rem ;
    }
    .btn {
      font-size: 0.8rem;
      padding: 0.35rem 0.6rem;
    }
    .nav-tabs .nav-item {
      flex: 1 1 auto;
      text-align: center;
    }
    .nav-tabs .nav-link {
      padding: 0.55rem;
      font-size: 0.85rem;
      color: #4ab3af;
    }
    form .mb-3 {
      margin-bottom: 0.9rem;
    }
    .sku-row {
      flex-direction: column;
    }
    .sku-row .form-control {
      width: 100%;
      margin-bottom: 0.5rem;
    }
    .table th,
    .table td {
      padding: 0.5rem;
      font-size: 0.85rem;
    }
    .input-group>:not(:first-child):not(.dropdown-menu):not(.valid-tooltip):not(.valid-feedback):not(.invalid-tooltip):not(.invalid-feedback) {
      width: 100% ;
    }
  }
</style>


</head>
<body class="p-4">
<?php include $_SERVER['DOCUMENT_ROOT'] . '/logoday.php'; ?>
  <div style="display: flex; justify-content: space-between;">
    <h1>📋 Đơn thủ công</h1>
    <a style="height: 100%;" href="?action=home" class="btn btn-secondary ms-2">🏠 Trang chủ</a>
  </div>

  <?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
  <?php endif; ?>
  <?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
  <?php endif; ?>

  <!-- Nav tabs -->
  <ul class="nav nav-tabs mb-3">
    <li class="nav-item">
      <a class="nav-link <?= (($_GET['tab'] ?? '') === 'manage' ? '' : 'active') ?>"
         href="?action=manualOrders">Tạo đơn</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= (($_GET['tab'] ?? '') === 'manage' ? 'active' : '') ?>"
         href="?action=manualOrders&tab=manage">Quản lý đơn hàng</a>
    </li>
  </ul>

  <?php if (($_GET['tab'] ?? '') === 'manage'): ?>
    <!-- PHẦN CHỌN KIỂU LỌC -->
    <div class="mb-3">
      <form method="get" style="display: inline-block;">
        <input type="hidden" name="action" value="manualOrders">
        <input type="hidden" name="tab" value="manage">
        <select name="filter" class="form-select d-inline-block w-auto" onchange="this.form.submit()">
          <option value="today" <?= ($filter === 'today' ? 'selected' : '') ?>>Hôm nay</option>
          <option value="7days" <?= ($filter === '7days' ? 'selected' : '') ?>>7 ngày qua</option>
          <option value="thismonth" <?= ($filter === 'thismonth' ? 'selected' : '') ?>>Tháng này</option>
          <option value="all" <?= ($filter === 'all' ? 'selected' : '') ?>>Toàn bộ</option>
        </select>
      </form>
    </div>

    <!-- Nếu không có đơn, hiển thị message -->
    <?php if (!empty($noOrdersMessage)): ?>
      <div class="alert alert-info"><?= htmlspecialchars($noOrdersMessage) ?></div>
    <?php endif; ?>

    <!-- Hiển thị bảng nếu có đơn hàng -->
    <?php if (!empty($orders)): ?>
      <div class="table-responsive">
        <table class="table table-bordered table-nay">
          <thead class="text-center align-middle">
            <tr>
              <th style="text-align: center !important;">STT</th>
              <th style="text-align: center !important;">Hình ảnh</th>
              <th style="text-align: center !important;">Khách hàng</th>
              <th style="text-align: center !important;">Số điện thoại</th>
              <th style="text-align: center !important;">Địa chỉ</th>
              <th style="text-align: center !important; width: 300px !important;">SKU</th>
              <th style="text-align: center !important;">Giá bán</th>
              <th style="text-align: center !important;">Phí ship</th>
              <th style="text-align: center !important;">Ngày tạo</th>
              <th style="text-align: center !important;">Hành động</th>
            </tr>
          </thead>
          <tbody>
            <?php $i = 1; foreach ($orders as $o): 
              $items = json_decode($o['sku_items'] ?? '[]', true);
            ?>
              <tr data-id="<?= $o['id'] ?>">
                <td class="text-center"><?= $i++ ?></td>
                <td class="text-center">
                  <img src="/quanlydonhang/public/<?= htmlspecialchars($o['order_image']) ?>"
                       width="80" class="view-image" data-src="/quanlydonhang/public/<?= htmlspecialchars($o['order_image']) ?>"
                       style="cursor:pointer;">
                </td>
                <td class="customer_name"><?= htmlspecialchars($o['customer_name']) ?></td>
                <td class="phone"><?= htmlspecialchars($o['phone']) ?></td>
                <td class="address"><?= htmlspecialchars($o['address']) ?></td>
                <td>
                  <table class="table table-sm mb-0 sku-table">
                    <thead class="text-center">
                      <tr>
                        <th class="text-start">SKU</th>
                        <th class="text-end">Số lượng</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($items as $it): ?>
                        <div class="text-start-text-end">
                          <tr class="text-start-text-end">
                            <td class="text-start"><?= htmlspecialchars($it['sku']) ?></td>
                            <td class="text-end"><?= (int)$it['qty'] ?></td>
                          </tr>
                        </div>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </td>
                <td class="text-center sale_price"><?= number_format($o['sale_price']) ?></td>
                <td class="text-center ship_fee"><?= number_format($o['ship_fee']) ?></td>
                <td class="text-center"><?= $o['created_at'] ?></td>
                <td class="text-center">
                  <button class="btn btn-sm btn-primary btn-edit green-btn">Sửa</button>
                  <button class="btn btn-sm btn-danger btn-delete red-btn">Xóa</button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

  <?php else: ?>
    <!-- Form tạo đơn -->
    <form action="?action=createOrder" method="post" enctype="multipart/form-data">
      <div class="mb-3">
        <label>Ảnh thông tin đơn</label>
        <input type="file" name="order_image" class="form-control">
      </div>
      <div class="mb-3">
        <label>Tên khách hàng</label>
        <input type="text" name="customer_name" class="form-control">
      </div>
      <div class="mb-3">
        <label>Số điện thoại</label>
        <input type="text" name="phone" class="form-control">
      </div>
      <div class="mb-3">
        <label>Địa chỉ</label>
        <textarea name="address" class="form-control"></textarea>
      </div>
      <div class="mb-3">
        <label>SKU &amp; Số lượng</label>
        <div id="sku-list">
          <div class="sku-row input-group mb-2">
            <input type="text" name="sku_hang_hoa[]" class="form-control sku-input" placeholder="SKU..." autocomplete="off">
            <input type="number" name="sku_quantity[]" class="form-control sku-qty" placeholder="Số lượng" min="0">
            <div class="list-group sku-results position-absolute w-100"></div>
          </div>
          <div class="sku-row input-group mb-2">
            <input type="text" name="sku_hang_hoa[]" class="form-control sku-input" placeholder="SKU..." autocomplete="off">
            <input type="number" name="sku_quantity[]" class="form-control sku-qty" placeholder="Số lượng" min="0">
            <div class="list-group sku-results position-absolute w-100"></div>
          </div>
        </div>
      </div>
      <div class="mb-3">
        <label>Giá bán</label>
        <input type="number" name="sale_price" class="form-control">
      </div>
      <div class="mb-3">
        <label>Phí ship (shop trả)</label>
        <input type="number" name="ship_fee" class="form-control">
      </div>
      <button class="btn btn-primary">Tạo đơn</button>
    </form>
  <?php endif; ?>

  <!-- Modal xem ảnh -->
  <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
      <div class="modal-content bg-transparent border-0">
        <div class="modal-body d-flex justify-content-center align-items-center p-0">
          <img src="" id="modalImage" class="img-fluid" style="max-height:100vh;">
        </div>
        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
      </div>
    </div>
  </div>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script 
  src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js" 
  integrity="sha512-+QV6WRYAK69cH46hfkBun9YQ0e+hjUwKcMBrr2xcV6sgOvR6Ynsz++nBj9s0+sX7fRk1J9/YdG4ZpdgpbOKH4w==" 
  crossorigin="anonymous">
</script>


<script>
$(function () {
  const maxRows = 50;          // giới hạn dòng SKU
  const $skuList = $('#sku-list');

  /* -------------------------------------------------- *
   *  TIỆN ÍCH CHUNG                                    *
   * -------------------------------------------------- */
  function createFormSkuRow () {
    return $(`
      <div class="sku-row input-group mb-2 position-relative">
        <input type="text" name="sku_hang_hoa[]" class="form-control sku-input" placeholder="SKU..." autocomplete="off">
        <input type="number" name="sku_quantity[]" class="form-control sku-qty" placeholder="Số lượng" min="0">
        <div class="list-group sku-results position-absolute w-100" style="z-index:1000;top:100%;left:0;"></div>
      </div>`);
  }

  function createEditSkuRow () {
    return $(`
      <tr class="sku-row-edit" style="display: flex; width: 332%;">
        <td>
          <div class="input-group mb-1 position-relative">
            <input style="width: 65%;" type="text" class="form-control sku-input-edit" placeholder="Thêm SKU..." autocomplete="off">
            <input style="width: 25%;" type="number" class="form-control sku-qty-edit" placeholder="Qty" min="0">
            <button type="button" class="btn btn-outline-danger btn-sm btn-remove-sku" style="display:none">×</button>
            <div class="list-group sku-results-edit position-absolute w-100" style="z-index:1000;top:100%;left:0;"></div>
          </div>
        </td>
      </tr>`);
  }

  function updateFilled ($row) {
    const $sku = $row.find('.sku-input, .sku-input-edit');
    const $qty = $row.find('.sku-qty, .sku-qty-edit');
    $sku.toggleClass('filled', $sku.val().trim() !== '');
    $qty.toggleClass('filled', (+$qty.val() || 0) > 0);
  }

  /* -------------------------------------------------- *
   *  AUTOCOMPLETE CHUNG                                *
   * -------------------------------------------------- */
  function bindAutocomplete ($container, inpSel, resSel) {
    // gõ tìm
    $container.on('input', inpSel, function () {
      const $inp = $(this), q = $inp.val().trim(), $res = $inp.siblings(resSel);
      if (!q) return $res.empty();
      $.getJSON('?action=ajaxSearchSku&term=' + encodeURIComponent(q), list => {
        $res.html(list.map(s => `<button type="button" class="list-group-item list-group-item-action sku-item">${s}</button>`).join(''));
      });
    });

    // chọn SKU
    $container.on('click', resSel + ' .sku-item', function () {
      const $btn = $(this), sku = $btn.text().trim(),
            $row = $btn.closest('.sku-row, .sku-row-edit');
      $row.find(inpSel).val(sku);
      $btn.parent().empty();
      $row.find('.sku-qty, .sku-qty-edit').focus();
      $row.find('.btn-remove-sku').show();
      updateFilled($row).trigger('input');
    });

    // click ngoài ẩn dropdown
    $(document).on('click', e => {
      if (!$(e.target).closest('.position-relative').length) $(resSel).empty();
    });
  }

  /* -------------------------------------------------- *
   *  FORM TẠO ĐƠN                                      *
   * -------------------------------------------------- */
  bindAutocomplete($skuList, '.sku-input', '.sku-results');

  $skuList.on('input', '.sku-input, .sku-qty', function () {
    const $row = $(this).closest('.sku-row');
    updateFilled($row);
    const isLast = $row.is($skuList.find('.sku-row').last()),
          skuVal = $row.find('.sku-input').val().trim();
    if (isLast && skuVal && $skuList.find('.sku-row').length < maxRows) {
      $skuList.append(createFormSkuRow());
    }
  });

  /* -------------------------------------------------- *
   *  QUẢN LÝ ĐƠN – XOÁ                                *
   * -------------------------------------------------- */
  $(document).on('click', '.btn-delete', function () {
    if (!confirm('Xác nhận xóa?')) return;
    $.post('?action=deleteOrder', { id: $(this).closest('tr').data('id') }, () => location.reload());
  });

  /* -------------------------------------------------- *
   *  QUẢN LÝ ĐƠN – SỬA / LƯU                          *
   * -------------------------------------------------- */
  $(document).on('click', '.btn-edit', function () {
    const $btn = $(this), $tr = $btn.closest('tr'), id = $tr.data('id');

    /* ---- LƯU ---- */
    if ($btn.text() === 'Lưu') {
      const fd = new FormData(); fd.append('id', id);
      const fi = $tr.find('input[type=file]')[0]; if (fi?.files[0]) fd.append(`order_image[${id}]`, fi.files[0]);
      ['customer_name','phone','address','sale_price','ship_fee'].forEach(f=>{
        fd.append(`${f}[${id}]`, $tr.find(`.${f} input, .${f} textarea`).val()||'');
      });
      $tr.find('.sku-row-edit').each((i,r)=>{
        fd.append(`sku_hang_hoa[${id}][${i}]`, $(r).find('.sku-input-edit').val()||'');
        fd.append(`sku_quantity[${id}][${i}]`, $(r).find('.sku-qty-edit').val()||0);
      });
      return $.ajax({url:'?action=updateOrder',type:'POST',data:fd,processData:false,contentType:false,success(){location.reload();}});
    }

    /* ---- CHẾ ĐỘ SỬA ---- */
    $btn.text('Lưu').toggleClass('btn-primary btn-success');
    ['customer_name','phone','address','sale_price','ship_fee'].forEach(f=>{
      const $c=$tr.find(`.${f}`), v=$c.text().trim();
      $c.html(f==='address'?`<textarea class="form-control">${v}</textarea>`:`<input type="${f.match(/price|fee/)?'number':'text'}" class="form-control" value="${v}" min="0">`);
    });
    $tr.find('td').eq(1).append(`<label class="btn btn-sm btn-outline-secondary mt-1 mb-0">Tải ảnh<input type="file" hidden accept="image/*"></label>`);

    const $newBody=$('<tbody>');
    $tr.find('.sku-table tbody tr').each((_,r)=>{
      const $row=createEditSkuRow();
      $row.find('.sku-input-edit').val($(r).children().eq(0).text().trim());
      $row.find('.sku-qty-edit').val($(r).children().eq(1).text().trim());
      if ($row.find('.sku-input-edit').val() || $row.find('.sku-qty-edit').val()) $row.find('.btn-remove-sku').show();
      updateFilled($row); $newBody.append($row);
    });
    $newBody.append(createEditSkuRow());
    $tr.find('.sku-table tbody').replaceWith($newBody);
    bindAutocomplete($newBody,'.sku-input-edit','.sku-results-edit');

    $newBody.on('input','.sku-input-edit, .sku-qty-edit',function(){
      const $row=$(this).closest('.sku-row-edit'); updateFilled($row);
      const isLast=$row.is($newBody.find('.sku-row-edit').last()),
            skuVal=$row.find('.sku-input-edit').val().trim(),
            qtyVal=+$row.find('.sku-qty-edit').val()||0;
      if ((skuVal||qtyVal)&&!$row.find('.btn-remove-sku').is(':visible')) $row.find('.btn-remove-sku').show();
      if (isLast&&skuVal&&$newBody.find('.sku-row-edit').length<maxRows) $newBody.append(createEditSkuRow());
    });
    $newBody.on('click','.btn-remove-sku',function(){ $(this).closest('tr').remove(); });
  });

  /* -------------------------------------------------- *
   *  MODAL ẢNH                                         *
   * -------------------------------------------------- */
  $(document).on('click','.view-image',function(){
    $('#modalImage').attr('src',$(this).data('src')); new bootstrap.Modal('#imageModal').show();
  });
  $(document).on('click','#imageModal',function(e){
    if(!$(e.target).closest('#modalImage').length) bootstrap.Modal.getInstance(this).hide();
  });

  /* -------------------------------------------------- *
   *  DATATABLES (chỉ tab manage)                       *
   * -------------------------------------------------- */
  if (location.search.includes('tab=manage')) {
    $('.table.table-bordered').DataTable({
      autoWidth: false,
      pageLength:30,lengthMenu:[[10,30,50,100],[10,30,50,100]],ordering:false,
      language:{lengthMenu:'Hiển thị _MENU_ dòng',paginate:{previous:'‹',next:'›'},info:'Hiển thị _START_–_END_ của _TOTAL_ đơn'}
    });
  }
});
</script>


</body>
</html>
