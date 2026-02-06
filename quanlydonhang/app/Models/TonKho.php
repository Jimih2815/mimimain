<?php
namespace App\Models;

use PDO;

class TonKho
{
    public static function getTonKho(PDO $db, string $skuHang): int
    {
        // Giả sử bạn có bảng ton_kho: (sku_hang_hoa, so_luong)
        $stmt = $db->prepare("SELECT so_luong
                                FROM ton_kho
                               WHERE sku_hang_hoa = :sku
                               LIMIT 1");
        $stmt->execute([':sku' => $skuHang]);
        return (int)($stmt->fetchColumn() ?: 0);
    }
}
