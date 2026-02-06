<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kết Quả Tính Giá Vốn</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- DataTables CSS -->
  <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/logoday.css">

  <style>
    /* Căn giữa header DataTables */
    table.dataTable thead th { text-align: center !important; }
    
    /* Tùy chỉnh style cho các card hiển thị kết quả */
    .card {
      border-radius: 10px;
    }
    .card-header {
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
    }
    .alert {
      font-size: 1.1rem;
    }
    
    /* Style cho block tiền về các sàn */
    #tienVeWrapper .tienVeRow {
      transition: all 0.3s ease;
    }
    #tienVeWrapper .tienVeRow:hover {
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .input-tienVe {
      border-radius: 0.25rem;
      padding: 0.5rem;
      font-weight: 500;
    }
    /* Style cho label trong block tiền về sau thuế và lãi ròng */
    .block-label {
      font-size: 1.1rem;
      font-weight: 600;
      color: #333;
    }
    .block-value {
      font-size: 1.3rem;
      font-weight: bold;
      color: #28a745;
    }
    /* Responsive margin cho section mới */
    .result-section {
      margin-top: 30px;
    }
  </style>
</head>
<body>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/logoday.php'; ?>
<div class="container my-5">

  <?php
  // Gom nhóm $results theo sku_hang_hoa
  $grouped = [];
  foreach ($results as $row) {
      $h = $row['sku_hang_hoa'];
      if (!isset($grouped[$h])) {
          $grouped[$h] = [
              'sku_hang_hoa' => $h,
              'gia_von'      => $row['gia_von'],
              'quantity'     => 0,
              'children'     => []
          ];
      }
      $grouped[$h]['quantity'] += $row['quantity'];
      $grouped[$h]['children'][] = $row['sku_gian_hang'];
  }
  $matched = array_values($grouped);
  ?>

  <h2 class="mb-4 text-primary text-center">📌 Kết Quả Tính Giá Vốn</h2>
  <div class="d-flex justify-content-center mb-3">
    <a href="?action=costCalcForm" class="btn btn-outline-success btn-sm me-2">🔄 Tính tiếp</a>
    <a href="?action=home" class="btn btn-outline-secondary btn-sm">🏠 Trang chủ</a>
  </div>

  <!-- BẢNG "SKU TRÙNG KHỚP" -->
  <?php if (!empty($matched)): ?>
  <div class="card shadow-sm mb-5">
    <div class="card-body">
      <h5 class="card-title text-center mb-4">✅ SKU trùng khớp</h5>
      <div class="table-responsive">
        <table id="matchedTable" class="table table-striped table-hover align-middle text-center">
          <thead class="table-dark">
            <tr>
              <th>STT</th>
              <th>SKU hàng hóa</th>
              <th>Số lượng</th>
              <th>Giá vốn</th>
              <th>Tổng giá vốn</th>
              <th>SKU gian hàng (con)</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($matched as $i => $info): 
              $slug = preg_replace('/[^a-z0-9]/','',strtolower($info['sku_hang_hoa']));
            ?>
            <tr>
              <td><?= $i+1 ?></td>
              <td><?= htmlspecialchars($info['sku_hang_hoa']) ?></td>
              <td><?= number_format($info['quantity']) ?></td>
              <td><?= number_format($info['gia_von']) ?></td>
              <td><?= number_format($info['quantity'] * $info['gia_von']) ?></td>
              <td>
                <div class="d-flex align-items-center justify-content-center">
                  <?php foreach (array_slice($info['children'],0,2) as $child): ?>
                    <span class="badge bg-secondary me-1">
                      <?= htmlspecialchars($child) ?>
                    </span>
                  <?php endforeach; ?>
                  <?php $cnt = count($info['children']);
                        if($cnt>2): ?>
                    <button type="button"
                            class="btn btn-link btn-sm p-0"
                            data-bs-toggle="modal"
                            data-bs-target="#modal-<?= $slug ?>">
                      +<?= $cnt-2 ?> xem
                    </button>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="alert alert-secondary text-center mt-3">
        <strong>Tổng số lượng (đã khớp):
          <span id="matchedQuantity">0</span>
        </strong>
      </div>
      <div class="alert alert-info text-center mt-3">
        <strong>Tổng giá vốn (đã khớp): 
          <span id="matchedTotal">0</span>
        </strong>
      </div>
    </div>
  </div>
  <?php endif; ?>


  <!-- ============ ĐƠN THỦ CÔNG THÁNG NÀY ============ -->
  <?php if(!empty($manualMatched)): ?>
  <div class="card shadow-sm mb-5">
    <div class="card-body">
      <h5 class="card-title text-center mb-4">📝 Đơn thủ công tháng này</h5>
      <div class="table-responsive">
        <table id="manualMatchedTable" class="table table-striped table-hover align-middle text-center">
          <thead class="table-dark">
            <tr>
              <th>STT</th>
              <th>SKU hàng hóa</th>
              <th>Số lượng</th>
              <th>Giá vốn</th>
              <th>Tổng giá vốn</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($manualMatched as $i=>$r): ?>
            <tr>
              <td><?= $i+1 ?></td>
              <td><?= htmlspecialchars($r['sku_hang_hoa']) ?></td>
              <td><?= number_format($r['quantity']) ?></td>
              <td><?= number_format($r['gia_von']) ?></td>
              <td><?= number_format($r['tong_von']) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="alert alert-secondary text-center mt-3">
        <strong>Tổng số lượng (đã khớp):
          <span id="manualMatchedQuantity">0</span>
        </strong>
      </div>
      <div class="alert alert-info text-center">
        <strong>Tổng giá vốn (đã khớp):
          <span id="manualMatchedTotal">0</span>
        </strong>
      </div>
    </div>
  </div>
  <?php endif; ?>


  <!-- BẢNG "SKU KHÔNG TRÙNG KHỚP" -->
  <?php if (!empty($unmatched)): ?>
  <div class="card shadow-sm mb-5">
    <div class="card-body">
      <h5 class="card-title text-center text-warning mb-4">⚠️ SKU không trùng khớp</h5>
      <div class="table-responsive">
        <table id="unmatchedTable" class="table table-striped table-hover align-middle text-center">
          <thead class="table-danger">
            <tr>
              <th>SKU gian hàng</th>
              <th>Số lượng</th>
              <th>Giá vốn (nhập tay)</th>
              <th>Tổng vốn</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($unmatched as $um): ?>
            <tr>
              <td><?= htmlspecialchars($um['sku_gian_hang']) ?></td>
              <td class="td-qty"><?= number_format($um['quantity']) ?></td>
              <td>
                <input type="number" min="0" class="form-control form-control-sm input-giaVon" value="<?= $um['gia_von'] ?>">
              </td>
              <td class="td-tongVon">0</td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="alert alert-warning text-center mt-3">
        <strong>Tổng giá vốn (chưa khớp): 
          <span id="unmatchedTotal">0</span>
        </strong>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- ============ SKU THỦ CÔNG KHÔNG TRÙNG KHỚP ============ -->
  <?php if(!empty($manualUnmatched)): ?>
  <div class="card shadow-sm mb-5">
    <div class="card-body">
      <h5 class="card-title text-center text-warning mb-4">⚠️ SKU thủ công không trùng khớp</h5>
      <div class="table-responsive">
        <table id="manualUnmatchedTable" class="table table-striped table-hover align-middle text-center">
          <thead class="table-danger">
            <tr>
              <th>SKU hàng hóa</th>
              <th>Số lượng</th>
              <th>Giá vốn (nhập tay)</th>
              <th>Tổng vốn</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($manualUnmatched as $r): ?>
            <tr>
              <td><?= htmlspecialchars($r['sku_hang_hoa']) ?></td>
              <td class="td-qty"><?= number_format($r['quantity']) ?></td>
              <td><input type="number" min="0" class="form-control form-control-sm input-giaVon" value="0"></td>
              <td class="td-tongVon">0</td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="alert alert-warning text-center mt-3">
        <strong>Tổng giá vốn (chưa khớp):
          <span id="manualUnmatchedTotal">0</span>
        </strong>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- TỔNG GIÁ VỐN TẤT CẢ -->
  <div class="alert alert-success text-center">
    <h4>Tổng giá vốn TẤT CẢ: <span id="finalTotal">0</span></h4>
  </div>
  <!-- NÚT LƯU DỮ LIỆU THÁNG NÀY -->
  <div class="text-center mb-5">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#saveMonthlyModal">
      💾 Lưu dữ liệu tháng này
    </button>
  </div>

  <!-- Modal cho phép chọn tháng, chọn hành động ghi đè hay cập nhật -->
  <div class="modal fade" id="saveMonthlyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form id="saveMonthlyForm" action="?action=saveMonthlyData" method="post">
          <input type="hidden" name="manualRevenue" id="manualRevenueInput">
          <div class="modal-header">
            <h5 class="modal-title">Lưu dữ liệu tháng này</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Chọn tháng (1-12)</label>
              <input type="number" name="month" class="form-control" min="1" max="12" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Chọn hành động nếu tháng đã tồn tại</label>
              <select name="actionType" class="form-select" required>
                <option value="override">Ghi đè dữ liệu</option>
                <option value="update">Cập nhật (merge) dữ liệu</option>
              </select>
            </div>
            <!-- Ẩn: matchedData, unmatchedData, manualMatchedData, manualUnmatchedData -->
            <input type="hidden" name="matchedData" id="matchedDataInput">
            <input type="hidden" name="unmatchedData" id="unmatchedDataInput">
            <input type="hidden" name="manualMatchedData" id="manualMatchedDataInput">
            <input type="hidden" name="manualUnmatchedData" id="manualUnmatchedDataInput">
            <!-- Ẩn: sumTienVe, tienVeSauThue, laiRong -->
            <input type="hidden" name="sumTienVe" id="sumTienVeInput">
            <input type="hidden" name="tienVeSauThue" id="tienVeSauThueInput">
            <input type="hidden" name="laiRong" id="laiRongInput">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-primary">Lưu dữ liệu</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- MỚI: Block nhập "tiền về các sàn", "tiền về sau thuế", "lãi ròng" -->
  <div class="card shadow-sm p-4 result-section">
    <div class="row align-items-center border-top pt-3">
      <div class="col-sm-6 text-end block-label">
          Tổng doanh thu đơn thủ công:
      </div>
      <div class="col-sm-6 text-start block-value"
          id="manualRevenue"
          data-val="<?= $manualRevenue ?>">
          <?= number_format($manualRevenue) ?>
      </div>
    </div>

    <div class="card-body">
      <h5 class="card-title text-primary mb-3">💰 Tiền về các sàn</h5>
      <!-- Vùng chứa các input tiền về -->
      <div id="tienVeWrapper">
        <div class="input-group mb-2 tienVeRow">
          <span class="input-group-text">₫</span>
          <input type="number" min="0" class="form-control input-tienVe" placeholder="Nhập số tiền">
        </div>
      </div>
      <!-- Hàng "tiền về sau thuế" -->
      <div class="row align-items-center my-3 border-top pt-3">
        <div class="col-sm-6 text-end block-label">Tiền về sau thuế (trừ 1,5%):</div>
        <div class="col-sm-6 text-start block-value" id="tienVeSauThue">0</div>
      </div>
      <!-- Hàng "lãi ròng" -->
      <div class="row align-items-center border-top pt-3">
        <div style="font-size: 1.6rem;" class="col-sm-6 text-end block-label">Lãi ròng:</div>
        <div style="color:red; font-size: 1.6rem;" class="col-sm-6 text-start block-value" id="laiRong">0</div>
      </div>
      <p class="text-muted small fst-italic mt-3">
        * Giá trị sẽ tự động cập nhật khi bạn thay đổi số tiền hoặc thao tác tìm kiếm trên bảng.
      </p>
    </div>
  </div>
</div>

<!-- Modal cho từng SKU hàng hóa -->
<?php foreach ($matched as $info):
  $slug = preg_replace('/[^a-z0-9]/','',strtolower($info['sku_hang_hoa']));
?>
<div class="modal fade" id="modal-<?= $slug ?>" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          SKU con của <?= htmlspecialchars($info['sku_hang_hoa']) ?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <ul class="list-group">
          <?php foreach ($info['children'] as $child): ?>
            <li class="list-group-item text-center">
              <?= htmlspecialchars($child) ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</div>
<?php endforeach; ?>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script>
(() => {
  // Cài đặt DataTables chung (không có ordering:false để cho phép sắp xếp)
  const opt = {
      pageLength: 30,
      lengthChange: false,
      language: {
          search: 'Tìm kiếm:',
          info: 'Hiển thị _START_–_END_ của _TOTAL_ mục',
          paginate: { previous: '‹', next: '›' }
      }
  };

  // Matched table: Chỉ cho phép sắp xếp ở cột "Số lượng" (index 2) và "Giá vốn" (index 3)
  const matchedDT = $('#matchedTable').length ?
      $('#matchedTable').DataTable({
          ...opt,
          columnDefs: [
              { orderable: false, targets: [0,1,4,5] }
          ],
          order: [[2, 'desc']]
      }) : null;

  // Unmatched table: Cho phép sắp xếp ở cột "Số lượng" (index 1) và "Giá vốn" (index 2)
  const unmatchedDT = $('#unmatchedTable').length ?
      $('#unmatchedTable').DataTable({
          ...opt,
          columnDefs: [
              { orderable: false, targets: [0,3] }
          ],
          order: [[1, 'desc']]
      }) : null;

  // Manual Matched table: Cho phép sắp xếp ở cột "Số lượng" (index 2) và "Giá vốn" (index 3)
  const manualMatchDT = $('#manualMatchedTable').length ?
      $('#manualMatchedTable').DataTable({
          ...opt,
          columnDefs: [
              { orderable: false, targets: [0,1,4] }
          ],
          order: [[2, 'desc']]
      }) : null;

  // Manual Unmatched table: Cho phép sắp xếp ở cột "Số lượng" (index 1) và "Giá vốn" (index 2)
  const manualUnmatchDT = $('#manualUnmatchedTable').length ?
      $('#manualUnmatchedTable').DataTable({
          ...opt,
          columnDefs: [
              { orderable: false, targets: [0,3] }
          ],
          order: [[1, 'desc']]
      }) : null;

  const fmt = n => new Intl.NumberFormat('vi-VN').format(n);
  function updateTotals(){
    const num = v => +v.toString().replace(/\D/g,'')||0;
    // 1) SKU trùng khớp
    let matchQty = 0, matchCost = 0;
    if (matchedDT){
      matchedDT.column(2, {search: 'applied'}).data().each(v => { matchQty += num(v); });
      matchedDT.column(4, {search: 'applied'}).data().each(v => { matchCost += num(v); });
      $('#matchedQuantity').text(fmt(matchQty));
      $('#matchedTotal').text(fmt(matchCost));
    }
    // 2) SKU không trùng khớp
    let unmatchCost = 0;
    if (unmatchedDT){
      unmatchedDT.rows({search: 'applied'}).every(function(){
        const $r = $(this.node()),
              qty = num($r.find('.td-qty').text()),
              gv  = +($r.find('.input-giaVon').val() || 0),
              cost = qty * gv;
        unmatchCost += cost; 
        $r.find('.td-tongVon').text(fmt(cost));
      });
      $('#unmatchedTotal').text(fmt(unmatchCost));
    }
    // 3) Đơn thủ công trùng khớp
    let mMatchQty = 0, mMatchCost = 0;
    if (manualMatchDT){
      manualMatchDT.column(2, {search: 'applied'}).data().each(v => { mMatchQty += num(v); });
      manualMatchDT.column(4, {search: 'applied'}).data().each(v => { mMatchCost += num(v); });
      $('#manualMatchedQuantity').text(fmt(mMatchQty));
      $('#manualMatchedTotal').text(fmt(mMatchCost));
    }
    // 4) SKU thủ công không trùng khớp
    let mUnmatchCost = 0;
    if (manualUnmatchDT){
      manualUnmatchDT.rows({search: 'applied'}).every(function(){
        const $r = $(this.node()),
              qty = num($r.find('.td-qty').text()),
              gv  = +($r.find('.input-giaVon').val() || 0),
              cost = qty * gv;
        mUnmatchCost += cost; 
        $r.find('.td-tongVon').text(fmt(cost));
      });
      $('#manualUnmatchedTotal').text(fmt(mUnmatchCost));
    }
    const grand = matchCost + unmatchCost + mMatchCost + mUnmatchCost;
    $('#finalTotal').text(fmt(grand));
  }
  function updateTienVe(){
    let sum = 0; 
    $('.input-tienVe').each(function(){ sum += +$(this).val() || 0; });
    const after  = Math.round(sum * 0.985),
          cost   = +$('#finalTotal').text().replace(/\D/g,'') || 0,
          rev    = +$('#manualRevenue').data('val') || 0,
          profit = after + rev - cost;
    $('#tienVeSauThue').text(fmt(after));
    $('#laiRong').text(fmt(profit));
  }
  [matchedDT, unmatchedDT, manualMatchDT, manualUnmatchDT].filter(Boolean)
    .forEach(dt => dt.on('draw', () => { updateTotals(); updateTienVe(); }));
  $(document).on('input', '.input-giaVon', () => { updateTotals(); updateTienVe(); });
  const maxTV = 10;
  $(document).on('input', '.input-tienVe', function(){
    const $rows = $('#tienVeWrapper .tienVeRow');
    if ($rows.length < maxTV && $rows.last().find('.input-tienVe').val().trim() !== ''){
      $('#tienVeWrapper').append(`
        <div class="input-group mb-2 tienVeRow">
          <span class="input-group-text">₫</span>
          <input type="number" min="0" class="form-control input-tienVe" placeholder="Nhập số tiền">
        </div>`);
    }
    updateTienVe();
  });
  $('#saveMonthlyForm').submit(function () {
    const num = v => +v.toString().replace(/\D/g, '') || 0;
    function dumpMatched(dt, isManual = false) {
      const out = [];
      if (!dt) return out;
      dt.rows().every(function () {
        const r = this.data();
        out.push({
          sku_hang_hoa: $(r[1]).text() || r[1],
          quantity:     num(r[2]),
          gia_von:      num(r[3]),
          tong_von:     num(r[4]),
          manual:       isManual ? 1 : 0
        });
      });
      return out;
    }
    function dumpUnmatched(dt, selectorSku, isManual = false) {
      const out = [];
      if (!dt) return out;
      dt.rows().every(function () {
        const $tr = $(this.node());
        out.push({
          sku_hang_hoa: $tr.children(selectorSku).text().trim(),
          quantity:     num($tr.find('.td-qty').text()),
          gia_von:      +$tr.find('.input-giaVon').val() || 0,
          tong_von:     0,
          manual:       isManual ? 1 : 0
        });
      });
      return out;
    }
    $('#matchedDataInput').val(JSON.stringify(dumpMatched(matchedDT)));
    $('#unmatchedDataInput').val(JSON.stringify(dumpUnmatched(unmatchedDT, ':first')));
    $('#manualMatchedDataInput').val(JSON.stringify(dumpMatched(manualMatchDT, true)));
    $('#manualUnmatchedDataInput').val(JSON.stringify(dumpUnmatched(manualUnmatchDT, ':first', true)));
    $('#manualRevenueInput').val($('#manualRevenue').data('val') || 0);
    const tv = [], re = /\D/g;
    $('.input-tienVe').each(function(){ const v = +$(this).val() || 0; if(v) tv.push(v); });
    $('#sumTienVeInput').val(JSON.stringify(tv));
    $('#tienVeSauThueInput').val(+$('#tienVeSauThue').text().replace(re,'') || 0);
    $('#laiRongInput').val(+$('#laiRong').text().replace(re,'') || 0);
  });
  updateTotals(); 
  updateTienVe();
})();
</script>

</body>
</html>
