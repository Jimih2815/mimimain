<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Home - SKU list</title>

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- DataTables CSS -->
  <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/logoday.css">

  <style>
  /* =========================
     Reset & Base Styles
  ============================ */
  html, body {
    margin: 0;
    padding: 0;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    background-color: #f8f9fa;
  }
  .container {
    padding: 0 15px;
  }

  /* =========================
     Headings & Buttons
  ============================ */
  h1 {
    font-size: 2rem;
    margin-bottom: 1rem;
  }
  .btn {
    font-size: 0.9rem;
  }
  
  /* =========================
     Table Styles
  ============================ */
  #homeTable {
    width: 100%;
    table-layout: auto;
    border-collapse: collapse;
  }
  #homeTable th,
  #homeTable td {
    padding: 0.75rem;
    vertical-align: middle;
    text-align: left;
  }
  #homeTable thead {
    background-color: #343a40;
    color: #fff;
  }
  /* Giới hạn cao mỗi row để đồng đều */
  #homeTable tbody tr {
    height: 4.5rem;
  }
  /* Đưa overflow cho bảng nếu nội dung vượt quá */
  .table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
  
  /* =========================
     SKU Children & Badges
  ============================ */
  .badge {
    font-size: 0.9rem;
    margin-right: 0.25rem;
  }
  /* Ẩn overflow trong ô con */
  .child-preview {
    max-height: 3rem;
    overflow: hidden;
  }

  /* =========================
     Modal Styles
  ============================ */
  .modal-dialog {
    margin: 1rem auto;
  }
  .modal-content {
    border-radius: 0.5rem;
  }
  .modal .modal-body {
    font-size: 1rem;
  }
  .list-group-item {
    font-size: 1rem;
    padding: 0.75rem;
  }

  /* =========================
     Responsive Styles (Mobile)
     Cho điện thoại (≤576px)
  ============================ */
  @media (max-width: 576px) {
    #homeTable {
      min-width: 1000px;
    }
    .btn-sm {
      margin-bottom: 0.5rem;
    }
    h1 {
      font-size: 1.5rem;
    }
    .btn {
      font-size: 0.8rem;
      padding: 0.35rem 0.6rem;
    }
    #homeTable th,
    #homeTable td {
      font-size: 0.85rem;
      padding: 0.5rem;
    }
    /* Nếu bảng quá rộng, cho phép kéo ngang */
    .table-responsive {
      overflow-x: auto;
    }
    /* Điều chỉnh modal cho nhỏ gọn hơn */
    .modal-dialog {
      margin: 0.5rem auto;
    }
    .modal .modal-body {
      font-size: 0.9rem;
    }
    .list-group-item {
      font-size: 0.9rem;
      padding: 0.5rem;
    }
  }
</style>

</head>
<body>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/logoday.php'; ?>
  <div class="container my-5">
    <h1 class="mb-4 text-primary">📦 Danh sách SKU</h1>
    <p>
      
      <a href="?action=costCalcForm" class="btn btn-outline-success btn-sm">💰 Tính giá vốn</a>
      <a href="?action=viewMonthlyHistory" class="btn btn-outline-primary btn-sm">
      🗓 Lịch sử dữ liệu tháng
      </a>
      <a href="?action=uploadForm" class="btn btn-outline-secondary btn-sm">🔄 Upload file</a>
      <a href="?action=manualOrders" class="btn btn-outline-warning btn-sm">🛒 Đơn hàng</a>
    </p>

    <form action="?action=saveHome" method="post">
      <div class="table-responsive">
        <table id="homeTable" class="table table-striped table-hover align-middle">
          <thead class="table-dark">
            <tr>
              <th>SKU hàng hóa</th>
              <th>Giá vốn</th>
              <th>Tồn kho</th>
              <th>Các SKU gian hàng (con)</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($grouped as $skuHang => $info): 
              // tạo slug cho id modal
              $slug = preg_replace('/[^a-z0-9]/', '', strtolower($skuHang));
            ?>
            <tr data-sku="<?= htmlspecialchars($skuHang) ?>">
              <td><?= htmlspecialchars($skuHang) ?></td>
              <td>
                <input type="number" min="0" name="gia_von[<?= htmlspecialchars($skuHang) ?>]"
                       class="form-control form-control-sm" value="<?= $info['gia_von'] ?>">
              </td>
              <td>
                <input type="number" min="0" name="ton_kho[<?= htmlspecialchars($skuHang) ?>]"
                       class="form-control form-control-sm" value="<?= $info['ton_kho'] ?>">
              </td>

              <td>
                <div class="d-flex justify-content-between align-items-center">
                  <!-- wrapper badges, căn giữa dọc -->
                  <div class="d-flex flex-wrap align-items-center">
                    <?php $children = $info['children']; ?>
                    <?php foreach (array_slice($children, 0, 1) as $child): ?>
                      <span class="badge bg-secondary me-1">
                        <?= htmlspecialchars($child['sku_gian_hang']) ?>
                      </span>
                    <?php endforeach; ?>

                    <?php if (count($children) > 1): ?>
                      <span class="badge bg-info text-dark me-1">
                      <?= count($children) - 1 ?> khác
                      </span>
                    <?php endif; ?>
                  </div>

                  <!-- nút Chỉnh sửa cũng căn giữa dọc -->
                  <div>
                    <button type="button"
                            class="btn btn-success btn-sm save-row me-1">
                      Lưu
                    </button>
                    <button type="button"
                            class="btn btn-outline-primary btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#modal-<?= $slug ?>">
                      Chỉnh sửa
                    </button>
                    <button type="button"
                            class="btn btn-outline-danger btn-sm delete-row">
                      Xoá
                    </button>
                  </div>
                </div>
              </td>



            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="text-end">
        <button type="submit" class="btn btn-primary">💾 Lưu hàng loạt</button>
      </div>
    </form>
  </div>

  <!-- Modal cho từng SKU hàng hóa -->
  <?php foreach ($grouped as $skuHang => $info): 
    $slug = preg_replace('/[^a-z0-9]/', '', strtolower($skuHang));
  ?>
  <div class="modal fade" id="modal-<?= $slug ?>" tabindex="-1" aria-labelledby="label-<?= $slug ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="label-<?= $slug ?>">
            SKU con của <?= htmlspecialchars($skuHang) ?>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <!-- Danh sách con hiện có -->
          <ul class="list-group mb-3">
            <?php foreach ($info['children'] as $child): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center" data-id="<?= $child['id'] ?>" data-sku="<?= htmlspecialchars($skuHang) ?>">
                <span class="me-2"><?= htmlspecialchars($child['sku_gian_hang']) ?></span>
                <button type="button" class="btn btn-sm btn-outline-danger remove-child">Xóa</button>
              </li>
            <?php endforeach; ?>
          </ul>

          <!-- Thêm SKU con mới -->
          <div class="input-group">
            <input type="text" class="form-control" placeholder="Thêm SKU con..." id="newChild-<?= $slug ?>">
            <button type="button" class="btn btn-outline-primary add-child" data-sku="<?= htmlspecialchars($skuHang) ?>">
              Thêm
            </button>
          </div>

        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- jQuery + DataTables JS -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script>
  $(document).ready(function(){
    $('#homeTable').DataTable({
      pageLength: 30,
      lengthChange: false,
      language: {
        search: "Tìm kiếm:",
        paginate: { previous: "‹", next: "›" },
        info: "Hiển thị _START_–_END_ của _TOTAL_ mục"
      },
      columnDefs: [
        { orderable: false, targets: 3 }
      ]
    });

    // Thêm SKU con qua AJAX
    $('.add-child').click(function(){
      const sku = $(this).data('sku');
      const slug = sku.toLowerCase().replace(/[^a-z0-9]/g,'');
      const input = $('#newChild-'+slug);
      const val = input.val().trim();
      if(!val) return;

      $.post('?action=addChildAjax', { skuHang: sku, childName: val }, function(resp){
        if(resp.success){
          // Thêm li mới với data-id
          const li = $('<li class="list-group-item d-flex justify-content-between align-items-center">')
            .attr('data-id', resp.id)
            .attr('data-sku', sku)
            .append('<span class="me-2">'+val+'</span>')
            .append('<button type="button" class="btn btn-sm btn-outline-danger remove-child">Xóa</button>');
          $('#modal-'+slug+' .list-group').append(li);
          input.val('').focus();
        }
      }, 'json');
    });

      // Xóa SKU con qua AJAX
      $(document).on('click','.remove-child', function(){
        const li = $(this).closest('li');
        const id = li.data('id');
        if(!id) return;
        $.post('?action=deleteChildAjax', { id: id }, function(resp){
          if(resp.success){
            li.remove();
          }
        }, 'json');
      });
    });
    $(function () {

/* ===== Lưu một dòng ===== */
$(document).on('click', '.save-row', function () {
  const $tr  = $(this).closest('tr');
  const sku  = $tr.data('sku');

  const gv  = $tr.find('input[name^="gia_von"]').val() || 0;
  const qty = $tr.find('input[name^="ton_kho"]').val() || 0;

  $.post('?action=saveSkuAjax',
    { sku: sku, gia_von: gv, ton_kho: qty },
    res => {
      if (res.success) {
        alert('Đã lưu!');
      } else {
        alert('Lưu thất bại');
      }
    }, 'json');
});

/* ===== Xoá một SKU hàng hoá ===== */
$(document).on('click', '.delete-row', function () {
  if (!confirm('Xoá SKU này và toàn bộ mapping?')) return;

  const $tr  = $(this).closest('tr');
  const sku  = $tr.data('sku');

  $.post('?action=deleteSkuAjax', { sku: sku }, res => {
    if (res.success) {
      // Nếu dùng DataTables:
      const table = $('#homeTable').DataTable();
      table.row($tr).remove().draw(false);
      // Nếu không dùng DataTables, chỉ cần $tr.remove();
    } else {
      alert('Không xoá được!');
    }
  }, 'json');
});

});
</script>
</body>
</html>
