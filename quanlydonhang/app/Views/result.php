<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Result</title></head>
<body>
  <h1>Kết quả</h1>
  <table border="1" cellpadding="5">
    <thead>
      <tr>
        <th>SKU gian hàng</th><th>SKU hàng hóa</th>
        <th>Quantity</th><th>Giá vốn</th><th>Tổng vốn</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($result as $r): ?>
      <tr>
        <td><?=htmlspecialchars($r['sku_gian_hang'],ENT_QUOTES)?></td>
        <td><?=htmlspecialchars($r['sku_hang_hoa'],ENT_QUOTES)?></td>
        <td><?=$r['quantity']?></td>
        <td><?=number_format($r['gia_von'])?></td>
        <td><?=number_format($r['tong_von'])?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <br><a href="?action=uploadForm">Quay lại</a>
</body>
</html>
