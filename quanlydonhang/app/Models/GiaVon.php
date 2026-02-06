<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

class GiaVon
{
    public static function import(PDO $db, array $rows): void
    {
        $db->beginTransaction();
        $stmt = $db->prepare(
            'INSERT INTO gia_von (sku_hang_hoa, gia_von, ngay_hieu_luc)
             VALUES (:sku, :price, :date)'
        );
    
        // Bỏ header (dòng đầu)
        array_shift($rows);
    
        // Duyệt từng dòng dữ liệu
        foreach ($rows as $r) {
            $stmt->execute([
                ':sku'   => trim((string)$r['A']),
                ':price' => (int)$r['B'],
                ':date'  => $r['C'],
            ]);
        }
    
        $db->commit();
    }
    

    public static function getGiaVon(PDO $db, ?string $skuHang): int
    {
        if ($skuHang === null) return 0;
        $stmt = $db->prepare(
            'SELECT gia_von FROM gia_von
             WHERE sku_hang_hoa=:sku AND ngay_hieu_luc<=CURDATE()
             ORDER BY ngay_hieu_luc DESC LIMIT 1'
        );
        $stmt->execute([':sku'=>$skuHang]);
        return (int)($stmt->fetchColumn() ?: 0);
    }
}
