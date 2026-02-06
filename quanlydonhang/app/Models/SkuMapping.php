<?php
declare(strict_types=1);

namespace App\Models;

use PDO;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SkuMapping
{
    /**
     * Import dữ liệu mapping từ file Excel.
     *
     * Cột A: SKU gian hàng  
     * Cột B: SKU hàng hóa  
     *
     * Nếu cặp (SKU gian hàng, SKU hàng hóa) đã tồn tại thì bỏ qua
     *
     * @param PDO   $db
     * @param array $mapData Mảng dữ liệu từ file Excel (key: A, B, …)
     */
    public static function import(PDO $db, array $mapData): void
    {
        foreach ($mapData as $row) {
            $skuGianHang = trim($row['A'] ?? '');
            $skuHangHoa  = trim($row['B'] ?? '');
    
            if ($skuGianHang === '' || $skuHangHoa === '') {
                continue;
            }
    
            /* 1) Đổi mọi placeholder thành tên thật */
            $db->prepare("
                UPDATE sku_mapping
                   SET sku_gian_hang = :gian
                 WHERE sku_hang_hoa  = :hang
                   AND sku_gian_hang = 'chưa ghép nối'
            ")->execute([':gian'=>$skuGianHang, ':hang'=>$skuHangHoa]);
    
            /* 2) Chèn mới (nếu chưa có) hoặc bật lại active */
            $db->prepare("
                INSERT INTO sku_mapping (sku_gian_hang, sku_hang_hoa, active)
                VALUES (:gian, :hang, 1)
                ON DUPLICATE KEY UPDATE active = VALUES(active)
            ")->execute([':gian'=>$skuGianHang, ':hang'=>$skuHangHoa]);
        }
    }
    
    
}
