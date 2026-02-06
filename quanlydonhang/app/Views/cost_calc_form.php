<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>💰 Tính Giá Vốn Theo SKU Gian Hàng</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/logoday.css">

</head>
<body>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/logoday.php'; ?>
  <div class="container my-5">
    <div class="card shadow-sm">
      <div class="card-header bg-success text-white">
        <h5 class="mb-0">💰 Tính Giá Vốn Theo SKU Gian Hàng</h5>
      </div>
      <div class="card-body">
        <form action="?action=processCostCalc" method="post" enctype="multipart/form-data">
          <div id="fileInputs">
            <div class="mb-3 file-input-row">
              <label class="form-label">Chọn file Bán hàng (A=SKU gian hàng, B=Số lượng)</label>
              <input class="form-control" type="file" name="costFile[]" accept=".xlsx" required>
            </div>
          </div>
          <button type="submit" class="btn btn-success">Tính giá vốn</button>
          <a href="?action=home" class="btn btn-secondary ms-2">🏠 Trang chủ</a>
        </form>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <script>
    $(function(){
      const maxFiles = 7;

      // Đánh dấu input đã upload: disable + bg-light
      function markUploaded($input) {
        // Chỉ thêm lớp bg-light để hiển thị xám nhạt,
        // không disable để file vẫn được gửi lên
        $input.addClass('bg-light');
      }


      $('#fileInputs').on('change', 'input[type=file]', function(){
        const $this = $(this);
        if ($this.val()) {
          markUploaded($this);
        }
        const rows = $('#fileInputs .file-input-row').length;
        if ($this.closest('.file-input-row').is(':last-child') && rows < maxFiles) {
          $('#fileInputs').append(`
            <div class="mb-3 file-input-row">
              <label class="form-label">Chọn thêm file (${rows+1})</label>
              <input class="form-control" type="file" name="costFile[]" accept=".xlsx">
            </div>
          `);
        }
      });
    });
  </script>
</body>
</html>
