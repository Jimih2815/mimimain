<?php
$matched        = $matched        ?? [];
$unmatched      = $unmatched      ?? [];
$manualMatched  = $mMatched       ?? [];
$manualUnmatch  = $mUnmatched     ?? [];
$tvArr          = json_decode($summary['list_tien_ve_san'] ?? '[]', true) ?: [];


function renderTable(
    string $id,
    string $title,
    array  $head,
    array  $rows,
    bool   $addTotals = false
){
    if (!$rows) return;

    echo '<div class="card mb-4 shadow-sm"><div class="card-body">';
    echo "<h5 class='card-title mb-3 text-center'>{$title}</h5>";

    echo '<div class="table-responsive">
            <table id="'.$id.'" class="table table-striped table-bordered align-middle text-center">
              <thead class="table-dark"><tr>';
    foreach ($head as $h) echo "<th>{$h}</th>";
    echo '  </tr></thead><tbody>';

    $i = 1;
    foreach ($rows as $r){
        echo '<tr>';
        if ($head[0] === 'STT'){                     
            echo '<td>'.$i++.'</td>';
            echo '<td>'.htmlspecialchars($r['sku_hang_hoa']).'</td>';
        }else{                                       
            echo '<td>'.htmlspecialchars($r['sku_hang_hoa']).'</td>';
        }
        echo '<td>'.number_format($r['quantity']).'</td>';
        echo '<td>'.number_format($r['gia_von']).'</td>';
        echo '<td>'.number_format($r['tong_von']).'</td>';
        echo '</tr>';
    }
    echo '</tbody></table></div>';

    if ($addTotals){
        echo '<div class="alert alert-secondary mt-3 text-center">
                <strong>Tổng số lượng: <span class="'.$id.'-qty">0</span></strong>
              </div>';
        echo '<div class="alert alert-info mt-2 text-center">
                <strong>Tổng giá vốn: <span class="'.$id.'-cost">0</span></strong>
              </div>';
    }
    echo '</div></div>';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Dữ liệu Tháng <?= $month ?>/<?= $year ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link rel="stylesheet" href="/logoday.css">

<style>
  .can-phai-bang-flex {
    display: flex;
    align-items: center;
    justify-content: flex-end;
  }
.container .text-primary {
  color:#4ab3af !important;
}
.table-dark > tr > th {
  background-color: #4ab3af;
  border-color: #3a9b98;
}
.active>.page-link, .page-link.active {
  background-color: #d1a029;
    border-color: #d1a029;
}
.page-link {
  color: #d1a029;
}
.page-link:hover {
  color: white;
  background-color: #d1a029;
  border-color: #d1a029;

}
.btn-secondary {
  background-color: #d1a029;
  border-color: #d1a029;
}
.btn-secondary:hover {
  background-color: #b18623;
  border-color: #b18623;

}
table.dataTable thead th{ text-align:center }
.financial-summary{
  background:#f8f9fa;border:2px solid #dee2e6;font-size:1.5rem;
  font-weight:bold;padding:20px;border-radius:10px
}
.disabled>.page-link, .page-link.disabled {
  color : #d1a029;
}
.financial-summary strong{ font-size:1.2rem }
@media only screen and (max-width: 1024px) { 
  .my-4, .container {
    margin : 1rem 0;
    width: 100%;
  }
  .container, .container-md, .container-sm {
    max-width: 100%;

  }
}
</style>
</head>
<body>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/logoday.php'; ?>
<div class="container my-4">
    <h2 class="text-center text-primary mb-4">
      Dữ liệu Tháng <?= $month ?>/<?= $year ?>
    </h2>

  <?php
  /* ------------------- 4 bảng ------------------- */
  renderTable(
      'tblMatch',
      '✅ SKU trùng khớp',
      ['STT','SKU hàng hóa','Số lượng','Giá vốn','Tổng vốn'],
      $matched,
      true
  );

  renderTable(
      'tblMMatch',
      '📝 Đơn thủ công tháng này',
      ['STT','SKU hàng hóa','Số lượng','Giá vốn','Tổng vốn'],
      $manualMatched,
      true
  );

  renderTable(
      'tblUnmatch',
      '⚠️ SKU không trùng khớp',
      ['SKU hàng hóa','Số lượng','Giá vốn','Tổng vốn'],   
      $unmatched,
      true
  );

  renderTable(
      'tblMUnmatch',
      '⚠️ SKU thủ công không trùng khớp',
      ['SKU hàng hóa','Số lượng','Giá vốn','Tổng vốn'],  
      $manualUnmatch,
      true
  );
  ?>

  <!-- --------- Tóm tắt tài chính --------- -->
  <div class="card mb-4 shadow-sm">
    <div class="card-body">
      <h5 class="card-title mb-3">📝 Tóm tắt tài chính</h5>
      <div class="card p-3 mb-3 financial-summary">
        <div><strong>Tiền về các sàn:</strong>
          <?php foreach($tvArr as $v): ?>
            <span class="badge bg-secondary me-1"
                  style="background:#fff!important;color:#198754;font-size:1.5rem">
              <?= number_format($v) ?>
            </span>
          <?php endforeach; ?>
        </div>
        <div><strong>Tiền về sau thuế:</strong>
          <span class="text-primary"><?= number_format($summary['tien_ve_sau_thue']) ?></span>
        </div>
        <div>
          <strong>Tổng doanh thu đơn thủ công:</strong>
          <span class="text-success">
            <?= number_format($summary['manual_revenue']) ?>
          </span>
        </div>

        <div><strong>Lãi ròng:</strong>
          <span class="text-danger"><?= number_format($summary['lai_rong']) ?></span>
        </div>
      </div>
    </div>
  </div>

  <!-- --------- Nút điều hướng --------- -->
  <div class="text-center mb-3 can-phai-bang-flex">
    <a href="?action=viewMonthlyHistory" class="btn btn-secondary ">
      ⏪ Quay lại danh sách tháng
    </a>
  </div>
</div>

<!-- ---------- Scripts ---------- -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function(){
  const opt = {
    pageLength:10,
    lengthMenu:[[10,20,30,50,100,-1],[10,20,30,50,100,'Tất cả']],
    language:{
      search:'Tìm kiếm:',
      lengthMenu:'Hiển thị _MENU_ dòng',
      info:'Hiển thị _START_–_END_ của _TOTAL_ mục',
      paginate:{previous:'‹',next:'›'}
    },
    ordering:false
  };
  const fmt = n => new Intl.NumberFormat('vi-VN').format(n);

  function init(id){
    if(!$('#'+id).length) return;
    const dt = $('#'+id).DataTable(opt);

    function calc(){
      let qty=0, cost=0;
      dt.column( id==='tblMatch'||id==='tblMMatch' ? 2 : 1 , {search:'applied'})
        .data().each(v => qty  += +v.toString().replace(/\D/g,'') || 0);
      dt.column( id==='tblMatch'||id==='tblMMatch' ? 4 : 3 , {search:'applied'})
        .data().each(v => cost += +v.toString().replace(/\D/g,'') || 0);
      $('.'+id+'-qty').text(fmt(qty));
      $('.'+id+'-cost').text(fmt(cost));
    }
    dt.on('draw', calc); calc();
  }

  ['tblMatch','tblMMatch','tblUnmatch','tblMUnmatch'].forEach(init);
});
</script>
</body>
</html>
