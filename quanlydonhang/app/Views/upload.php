<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>🔄 Upload Dữ Liệu</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/logoday.css">

</head>
<body>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/logoday.php'; ?>
  <div class="container my-5">
    <div class="card shadow-sm">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0">🔄 Upload Dữ Liệu</h5>
      </div>
      <div class="card-body">
        <form action="?action=processUpload" method="post" enctype="multipart/form-data">

          <!-- Mapping Files -->
          <div class="mb-4">
            <label class="form-label">Mapping Files (2 cột: A=SKU gian hàng, B=SKU hàng hóa)</label>
            <div id="mappingInputs">
              <div class="input-group mb-2 mapping-row">
                <input type="file" name="mapping[]" class="form-control" accept=".xlsx">
              </div>
            </div>
          </div>

          <!-- StockSales Files -->
          <div class="mb-4">
            <label class="form-label">StockSales Files (3 cột: A=SKU hàng hóa, B=Giá vốn, C=Số lượng)</label>
            <div id="stockInputs">
              <div class="input-group mb-2 stock-row">
                <input type="file" name="stockSales[]" class="form-control" accept=".xlsx">
              </div>
            </div>
          </div>

          <button type="submit" class="btn btn-primary">Xử lý dữ liệu</button>
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

      // Hàm để disable và làm xám input khi đã chọn file
      function markUploaded(input) {
        // input.prop('disabled', true)
             .addClass('bg-light')
             .closest('.input-group').find('label').addClass('text-muted');
      }

      // Mapping inputs
      $('#mappingInputs').on('change', 'input[type=file]', function(){
        const $this = $(this);
        if ($this.val()) {
          markUploaded($this);
        }
        const rows = $('#mappingInputs .mapping-row').length;
        if ($this.closest('.mapping-row').is(':last-child') && rows < maxFiles) {
          $('#mappingInputs').append(`
            <div class="input-group mb-2 mapping-row">
              <input type="file" name="mapping[]" class="form-control" accept=".xlsx">
            </div>
          `);
        }
      });

      // StockSales inputs
      $('#stockInputs').on('change', 'input[type=file]', function(){
        const $this = $(this);
        if ($this.val()) {
          markUploaded($this);
        }
        const rows = $('#stockInputs .stock-row').length;
        if ($this.closest('.stock-row').is(':last-child') && rows < maxFiles) {
          $('#stockInputs').append(`
            <div class="input-group mb-2 stock-row">
              <input type="file" name="stockSales[]" class="form-control" accept=".xlsx">
            </div>
          `);
        }
      });
    });
  </script>
</body>
</html>
