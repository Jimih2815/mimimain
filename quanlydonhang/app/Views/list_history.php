<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Lịch sử dữ liệu</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/logoday.css">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <style>
    .container .text-primary {
      color: #4ab3af !important;
    }
    .list-group .list-group-item .bg-info{
      background-color: #d1a029 !important;
      color:white !important;
    }
    @media only screen and (max-width: 1024px) {
  .container, .container-md, .container-sm {
    width: 100%;
  }
}
@media only screen and (max-width: 768px) {
  .container {
    margin:0;
  }
  .text-primary {
    font-size: 1.3rem;

  }
}
  </style>
</head>
<body>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/logoday.php'; ?>
<div class="container my-5">
  <h2 class="mb-4 text-primary">🗓 Lịch sử dữ liệu các tháng đã lưu</h2>

  <?php if (!empty($rows)): ?>
    <!-- Danh sách các tháng dưới dạng list-group -->
    <div class="list-group">
      <?php foreach ($rows as $r): ?>
        <a href="?action=viewMonthlyData&year=<?= $r['year'] ?>&month=<?= $r['month'] ?>"
           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
          <span><strong>Tháng <?= $r['month'] ?>/<?= $r['year'] ?></strong></span>
          <span class="badge bg-info text-dark">Xem chi tiết</span>
        </a>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="alert alert-warning">Chưa có dữ liệu tháng nào được lưu.</div>
  <?php endif; ?>

</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
