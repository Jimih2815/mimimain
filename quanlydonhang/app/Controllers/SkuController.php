<?php
declare(strict_types=1);

namespace App\Controllers;

use PDO;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\SkuMapping;

class SkuController
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function uploadForm(): void
    {
        // Giao diện upload
        include __DIR__ . '/../Views/upload.php';
    }

    public function processUpload(): void
{
    $db = $this->db;
    $db->beginTransaction();

    try {
        $didSomething = false;

        /* ===== 1) Mapping ===== */
        if (!empty($_FILES['mapping']['tmp_name'][0])) {
            foreach ($_FILES['mapping']['tmp_name'] as $mapPath) {
                if (!is_uploaded_file($mapPath)) continue;

                $mapData = IOFactory::load($mapPath)
                                    ->getActiveSheet()
                                    ->toArray(null, true, true, true);

                /* Bỏ header linh hoạt */
                $firstRow = reset($mapData);
                $colA     = strtolower(trim((string)($firstRow['A'] ?? '')));
                if (str_contains($colA, 'sku')) {
                    unset($mapData[key($mapData)]);
                }
                $mapData = array_values($mapData);   // re‑index

                /* Lọc bỏ dòng trống hoặc thiếu cột */
                $clean = [];
                foreach ($mapData as $row) {
                    $a = trim((string)($row['A'] ?? ''));
                    $b = trim((string)($row['B'] ?? ''));
                    if ($a === '' || $b === '') continue;   // ← chỉ skip, không lỗi
                    $clean[] = ['A' => $a, 'B' => $b];
                }

                if ($clean) {                     // có dữ liệu hợp lệ mới import
                    SkuMapping::import($db, $clean);
                    $didSomething = true;
                }
            }
        }


        /* ===== 2) StockSales ===== */
        if (!empty($_FILES['stockSales']['tmp_name'][0])) {
            foreach ($_FILES['stockSales']['tmp_name'] as $ssPath) {
                if (!is_uploaded_file($ssPath)) continue;

                $ssData = IOFactory::load($ssPath)
                                   ->getActiveSheet()
                                   ->toArray(null, true, true, true);

                /* Bỏ header */
                $firstRow = reset($ssData);
                $colA     = strtolower(trim((string)($firstRow['A'] ?? '')));
                if (str_contains($colA, 'sku')) {
                    unset($ssData[key($ssData)]);
                }
                $ssData = array_values($ssData);

                /* Lọc & cập nhật DB */
                foreach ($ssData as $row) {
                    $skuHang = trim((string)($row['A'] ?? ''));
                    $giaVon  = trim((string)($row['B'] ?? ''));
                    $soLuong = trim((string)($row['C'] ?? ''));

                    if ($skuHang === '' && $giaVon === '' && $soLuong === '') continue; // dòng trống
                    if ($skuHang === '') {
                        throw new \RuntimeException('file excel sai định dạng');
                    }
                    // Nếu để trống coi như 0
                    $giaVon  = $giaVon  === '' ? 0 : (is_numeric($giaVon)  ? (int)$giaVon  : throw new \RuntimeException('file excel sai định dạng'));
                    $soLuong = $soLuong === '' ? 0 : (is_numeric($soLuong) ? (int)$soLuong : throw new \RuntimeException('file excel sai định dạng'));

                    $this->ensureSkuHangHoaExists($skuHang);
                    $this->updateGiaVon($skuHang, $giaVon);
                    $this->updateTonKho($skuHang, $soLuong);
                }
                $didSomething = true;
            }
        }

        if (!$didSomething) {
            throw new \RuntimeException('Bạn chưa chọn file để xử lý');
        }

        $db->commit();
        header('Location: ?action=home');
        exit;

    } catch (\Throwable $e) {
        $db->rollBack();
        echo "<h3 style='color:red;text-align:center;margin-top:2rem'>{$e->getMessage()}</h3>";
        exit;
    }
}

    
    
    


    // Các phương thức khác (home, saveHome, helper methods, ...)
    // Không cần thay đổi phần còn lại nếu chỉ muốn sửa phần upload mapping
    
    private function ensureSkuHangHoaExists(string $skuHang): void
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM sku_mapping
             WHERE sku_hang_hoa = :sku
        ");
        $stmt->execute([':sku' => $skuHang]);
        $cnt = (int)$stmt->fetchColumn();
    
        if ($cnt === 0) {
            $ins = $this->db->prepare("
                INSERT INTO sku_mapping (sku_gian_hang, sku_hang_hoa, active)
                VALUES ('chưa ghép nối', :hang, 1)
            ");
            $ins->execute([':hang' => $skuHang]);
        }
    }
    
    private function updateGiaVon(string $skuHang, int $price): void
    {
        $skuHang = trim($skuHang);
        $sql = "INSERT INTO gia_von (sku_hang_hoa, gia_von)
                VALUES (:sku, :price)
                ON DUPLICATE KEY UPDATE gia_von=:p2";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':sku' => $skuHang,
            ':price' => $price,
            ':p2' => $price,
        ]);
    }
    
    private function updateTonKho(string $skuHang, int $soLuong): void
    {
        $sql = "INSERT INTO ton_kho (sku_hang_hoa, so_luong)
                VALUES (:sku, :qty)
                ON DUPLICATE KEY UPDATE so_luong=:q2";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':sku' => $skuHang,
            ':qty' => $soLuong,
            ':q2' => $soLuong,
        ]);
    }

    private function getGiaVon(string $skuHang): int
    {
        $stmt = $this->db->prepare("
            SELECT gia_von FROM gia_von
             WHERE sku_hang_hoa=:sku
             LIMIT 1
        ");
        $stmt->execute([':sku' => $skuHang]);
        return (int)($stmt->fetchColumn() ?: 0);
    }

    private function getTonKho(string $skuHang): int
    {
        $stmt = $this->db->prepare("
            SELECT so_luong FROM ton_kho
             WHERE sku_hang_hoa=:sku
             LIMIT 1
        ");
        $stmt->execute([':sku' => $skuHang]);
        return (int)($stmt->fetchColumn() ?: 0);
    }
    public function saveHome(): void
{
    // 1) Nhận dữ liệu POST
    // VD: $_POST['gia_von'][$skuHang] = giá vốn
    //     $_POST['ton_kho'][$skuHang] = số lượng tồn kho
    //     $_POST['children'][$skuHang] = mảng các SKU gian hàng đã chỉnh sửa (nếu có)
    //     $_POST['new_child'][$skuHang] = SKU gian hàng mới (nếu có)
    $giaVonArr   = $_POST['gia_von']   ?? [];
    $tonKhoArr   = $_POST['ton_kho']   ?? [];
    $childrenArr = $_POST['children']  ?? [];
    $newChildArr = $_POST['new_child'] ?? [];

    // 2) Update giá vốn (gia_von) và tồn kho (ton_kho) cho mỗi SKU hàng hóa
    foreach ($giaVonArr as $skuHang => $price) {
        $this->updateGiaVon((string)$skuHang, (int)$price);   
    }
    foreach ($tonKhoArr as $skuHang => $qty) {
        $this->updateTonKho((string)$skuHang, (int)$qty);     
    }

    // 3) Cập nhật hoặc tái chèn các SKU gian hàng (children)
    // Xóa hết các SKU gian hàng cũ rồi chèn lại
    foreach ($childrenArr as $skuHang => $childList) {
        // Xóa tất cả các dòng có sku_hang_hoa = $skuHang
        $del = $this->db->prepare("DELETE FROM sku_mapping WHERE sku_hang_hoa = :sku");
        $del->execute([':sku' => $skuHang]);

        // Nếu không có SKU gian hàng nào thì chèn bản ghi mặc định 'chưa ghép nối'
        if (empty($childList)) {
            $ins = $this->db->prepare("
                INSERT INTO sku_mapping (sku_gian_hang, sku_hang_hoa, active)
                VALUES ('chưa ghép nối', :hang, 1)
            ");
            $ins->execute([':hang' => $skuHang]);
            continue;
        }

        // Nếu có các SKU gian hàng, chèn từng SKU một
        foreach ($childList as $childName) {
            $childName = trim($childName);
            if ($childName === '') {
                continue;
            }
            $ins = $this->db->prepare("
                INSERT INTO sku_mapping (sku_gian_hang, sku_hang_hoa, active)
                VALUES (:gian, :hang, 1)
            ");
            $ins->execute([
                ':gian' => $childName,
                ':hang' => $skuHang,
            ]);
        }
    }

    // 4) Chèn thêm SKU gian hàng mới (nếu có)
    foreach ($newChildArr as $skuHang => $childName) {
        $childName = trim($childName);
        if ($childName === '') {
            continue;
        }
        $ins = $this->db->prepare("
            INSERT INTO sku_mapping (sku_gian_hang, sku_hang_hoa, active)
            VALUES (:gian, :hang, 1)
        ");
        $ins->execute([
            ':gian' => $childName,
            ':hang' => $skuHang,
        ]);
    }

    // 5) Sau khi lưu xong, redirect về trang home
    header('Location: ?action=home');
    exit;
}

    // Hiển thị form upload tính giá vốn
    public function costCalcForm(): void
    {
        include __DIR__ . '/../Views/cost_calc_form.php';
    }
    public function home(): void
{
    // Lấy toàn bộ sku_mapping, gom theo sku_hang_hoa
    $sql = "SELECT id, sku_gian_hang, sku_hang_hoa, active
              FROM sku_mapping
          ORDER BY sku_hang_hoa, id ASC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Gom nhóm theo sku_hang_hoa
    $grouped = [];
    foreach ($rows as $r) {
        $skuHang = $r['sku_hang_hoa'];
        if (!isset($grouped[$skuHang])) {
            $grouped[$skuHang] = [
                'sku_hang_hoa' => $skuHang,
                'gia_von'      => 0,
                'ton_kho'      => 0,
                'children'     => [],
            ];
        }
        // Thêm SKU gian hàng (con)
        $grouped[$skuHang]['children'][] = [
            'id'            => $r['id'],
            'sku_gian_hang' => $r['sku_gian_hang'],
            'active'        => $r['active'],
        ];
    }

    // Lấy giá vốn và tồn kho cho từng sku_hang_hoa
    foreach ($grouped as $skuHang => &$val) {
        $val['gia_von'] = $this->getGiaVon($skuHang);
        $val['ton_kho'] = $this->getTonKho($skuHang);
    }
    unset($val);

    // Gọi view hiển thị trang home (đảm bảo file views/home.php tồn tại)
    include __DIR__ . '/../Views/home.php';
}

/* ------------------------------------------------------------------
 *  TÍNH GIÁ VỐN TỪ FILE + ĐƠN THỦ CÔNG TRONG THÁNG HIỆN TẠI
 * -----------------------------------------------------------------*/
public function processCostCalc(): void
{
    /* ========= 1. ĐỌC FILE EXCEL ========= */
    if (empty($_FILES['costFile']['tmp_name'][0])) {
        die('No file uploaded!');
    }

    $rows = [];
    foreach ($_FILES['costFile']['tmp_name'] as $tmp) {
        if (!is_uploaded_file($tmp)) continue;

        $sheet = IOFactory::load($tmp)->getActiveSheet()->toArray(null, true, true, true);
        array_shift($sheet);               // bỏ hàng tiêu đề
        $rows = array_merge($rows, $sheet);
    }

    /* ========= 2. GỘP THEO SKU GIAN HÀNG ========= */
    $skuQtyMap = [];                       // [sku_gian_hang => tổng_qty]
    foreach ($rows as $r) {
        $sku = trim((string)($r['A'] ?? ''));
        $qty = (int)($r['B'] ?? 0);
        if ($sku === '' || $qty === 0) continue;
        $skuQtyMap[$sku] = ($skuQtyMap[$sku] ?? 0) + $qty;
    }

    /* ========= 3. SO KHỚP SKU TỪ FILE ========= */
    $results   = [];                       // matched (đã có giá vốn)
    $unmatched = [];                       // chưa có giá vốn

    $stmtMap = $this->db->prepare("
        SELECT sku_hang_hoa
          FROM sku_mapping
         WHERE LOWER(REPLACE(sku_gian_hang,' ','')) = LOWER(REPLACE(:g,' ','')) 
         LIMIT 1
    ");
    $stmtGv  = $this->db->prepare("
        SELECT gia_von
          FROM gia_von
         WHERE LOWER(REPLACE(sku_hang_hoa,' ','')) = LOWER(REPLACE(:s,' ','')) 
         LIMIT 1
    ");

    foreach ($skuQtyMap as $skuGian => $qty) {
        /* -- tìm sku_hang_hoa tương ứng -- */
        $stmtMap->execute([':g' => $skuGian]);
        $skuHang = $stmtMap->fetchColumn();      // đúng chuẩn PDOStatement

        if (!$skuHang) {                         // chưa mapping
            $unmatched[] = [
                'sku_gian_hang' => $skuGian,
                'quantity'      => $qty,
                'gia_von'       => 0
            ];
            continue;
        }

        /* -- lấy giá vốn của sku_hang_hoa -- */
        $stmtGv->execute([':s' => $skuHang]);
        $gv = (int)($stmtGv->fetchColumn() ?: 0);

        $results[] = [
            'sku_gian_hang' => $skuGian,
            'sku_hang_hoa'  => $skuHang,
            'quantity'      => $qty,
            'gia_von'       => $gv,
            'tong_von'      => $gv * $qty
        ];
    }

    /* ========= 4. XỬ LÝ ĐƠN THỦ CÔNG (orders) ========= */
    $year  = (int)date('Y');
    $month = (int)date('n');

    /* 4A. gom số lượng theo SKU (người dùng nhập) */
$manualQty = [];                         
$stmtOrd = $this->db->prepare("
    SELECT sku_items
      FROM orders
     WHERE YEAR(created_at)=:y AND MONTH(created_at)=:m
");
$stmtOrd->execute([':y' => $year, ':m' => $month]);
while ($row = $stmtOrd->fetch(PDO::FETCH_ASSOC)) {
    foreach (json_decode($row['sku_items'] ?? '[]', true) as $it) {
        $skuGian = trim((string)($it['sku'] ?? ''));
        $q       = (int)($it['qty'] ?? 0);
        if ($skuGian === '' || $q === 0) continue;
        $manualQty[$skuGian] = ($manualQty[$skuGian] ?? 0) + $q;
    }
}

/* 4B. phân loại matched / unmatched cho đơn thủ công */
$manualMatched   = [];
$manualUnmatched = [];

// Chuẩn bị 2 câu SQL:
// 1) Lấy sku_hang_hoa từ sku_mapping
$stmtMapGianToHang = $this->db->prepare("
    SELECT sku_hang_hoa
      FROM sku_mapping
     WHERE sku_gian_hang = :gian
     LIMIT 1
");
// 2) Lấy gia_von từ bảng gia_von
$stmtGetGiaVon = $this->db->prepare("
    SELECT gia_von
      FROM gia_von
     WHERE sku_hang_hoa = :hang
     LIMIT 1
");

foreach ($manualQty as $skuGian => $qty) {
    // tìm sku_hang_hoa
    $stmtMapGianToHang->execute([':gian' => $skuGian]);
    $skuHang = $stmtMapGianToHang->fetchColumn();

    if ($skuHang) {
        // có mapping, lấy giá vốn
        $stmtGetGiaVon->execute([':hang' => $skuHang]);
        $gv = (int)($stmtGetGiaVon->fetchColumn() ?: 0);

        if ($gv > 0) {
            $manualMatched[] = [
                'sku_hang_hoa' => $skuHang,
                'quantity'     => $qty,
                'gia_von'      => $gv,
                'tong_von'     => $gv * $qty,
            ];
        } else {
            // mapping nhưng chưa có giá vốn
            $manualUnmatched[] = [
                'sku_hang_hoa' => $skuHang,
                'quantity'     => $qty,
                'gia_von'      => 0,
            ];
        }
    } else {
        // không tìm thấy mapping
        $manualUnmatched[] = [
            'sku_hang_hoa' => $skuGian,
            'quantity'     => $qty,
            'gia_von'      => 0,
        ];
    }
}


    /* 4C. tính doanh thu đơn thủ công */
    $manualRevenue = 0;
    $stmtRev = $this->db->prepare("
        SELECT sale_price, ship_fee
          FROM orders
         WHERE YEAR(created_at)=:y AND MONTH(created_at)=:m
    ");
    $stmtRev->execute([':y' => $year, ':m' => $month]);
    while ($r = $stmtRev->fetch(PDO::FETCH_ASSOC)) {
        $manualRevenue += ((int)$r['sale_price'] - (int)$r['ship_fee']);
    }

    /* ========= 5. HIỂN THỊ KẾT QUẢ ========= */
    include __DIR__ . '/../Views/cost_calc_result.php';
}


    
    

    // AJAX: Thêm 1 SKU con
    public function addChildAjax(): void
    {
        $skuHang   = $_POST['skuHang']   ?? '';
        $childName = trim($_POST['childName'] ?? '');
        if ($skuHang && $childName) {
            // Chèn vào DB
            $stmt = $this->db->prepare("
                INSERT INTO sku_mapping (sku_gian_hang, sku_hang_hoa, active)
                VALUES (:gian, :hang, 1)
            ");
            $stmt->execute([':gian'=>$childName, ':hang'=>$skuHang]);
            $id = (int)$this->db->lastInsertId();
            // Trả về JSON
            header('Content-Type: application/json');
            echo json_encode(['success'=>true, 'id'=>$id]);
            return;
        }
        http_response_code(400);
        echo json_encode(['success'=>false]);
    }

    // AJAX: Xóa 1 SKU con theo id
    public function deleteChildAjax(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $stmt = $this->db->prepare("DELETE FROM sku_mapping WHERE id=:id");
            $stmt->execute([':id'=>$id]);
            header('Content-Type: application/json');
            echo json_encode(['success'=>true]);
            return;
        }
        http_response_code(400);
        echo json_encode(['success'=>false]);
    }

    public function saveMonthlyData(): void
    {
        $month  = (int)($_POST['month'] ?? 0);
        if ($month<1 || $month>12) die('Tháng không hợp lệ');
        $year   = (int)date('Y');
        $action = $_POST['actionType'] ?? 'override';         // override | update
    
        /* nhận dữ liệu */
        $matchedArr        = json_decode($_POST['matchedData']        ?? '[]', true) ?: [];
        $unmatchedArr      = json_decode($_POST['unmatchedData']      ?? '[]', true) ?: [];
        $manualMatchedArr  = json_decode($_POST['manualMatchedData']  ?? '[]', true) ?: [];
        $manualUnmatchedArr= json_decode($_POST['manualUnmatchedData']?? '[]', true) ?: [];
        $manualRevenue     = (int)($_POST['manualRevenue'] ?? 0);
    
        /* chuẩn hoá & gộp */
        $norm = function($r){
            $r['sku_hang_hoa'] = trim((string)($r['sku_hang_hoa'] ?? 'N/A'));
            $r['quantity']     = (int)($r['quantity'] ?? 0);
            $r['gia_von']      = (int)($r['gia_von']  ?? 0);
            $r['tong_von']     = (int)($r['tong_von'] ?? $r['quantity']*$r['gia_von']);
            return $r;
        };
        $all = array_map($norm, array_merge($matchedArr,$unmatchedArr,$manualMatchedArr,$manualUnmatchedArr));
    
        /* xoá cũ nếu override */
        if ($action==='override') {
            $this->db->prepare("DELETE FROM cost_calc_history WHERE year=? AND month=?")->execute([$year,$month]);
        }
    
        /* nếu update => cộng gộp */
        if ($action==='update') {
            $old=[];
            $st=$this->db->prepare("SELECT sku_hang_hoa,quantity,gia_von,tong_von FROM cost_calc_history WHERE year=? AND month=?");
            $st->execute([$year,$month]);
            while($r=$st->fetch(PDO::FETCH_ASSOC)) $old[$r['sku_hang_hoa']]=$r;
    
            foreach($all as $r){
                if(isset($old[$r['sku_hang_hoa']])){
                    $old[$r['sku_hang_hoa']]['quantity'] += $r['quantity'];
                    $old[$r['sku_hang_hoa']]['tong_von'] += $r['tong_von'];
                }else $old[$r['sku_hang_hoa']]=$r;
            }
            $all=array_values($old);
            $this->db->prepare("DELETE FROM cost_calc_history WHERE year=? AND month=?")->execute([$year,$month]);
        }
    
        /* insert history */
        $ins=$this->db->prepare("
            INSERT INTO cost_calc_history
                   (year,month,sku_hang_hoa,quantity,gia_von,tong_von,source)
            VALUES (:y,:m,:sku,:q,:gv,:tv,:src)
        ");
        foreach($all as $r){
            $ins->execute([
                ':y'=>$year,':m'=>$month,':sku'=>$r['sku_hang_hoa'],
                ':q'=>$r['quantity'],':gv'=>$r['gia_von'],':tv'=>$r['tong_von'],
                ':src'=>($r['manual']??0)?'manual':'file'
            ]);
        }
    
        /* lưu summary */
        $this->db->prepare("DELETE FROM cost_calc_summary WHERE year=? AND month=?")->execute([$year,$month]);
        $this->db->prepare("
            INSERT INTO cost_calc_summary
                   (year,month,list_tien_ve_san,tien_ve_sau_thue,
                    manual_revenue,lai_rong)
            VALUES (?,?,?,?,?,?)
        ")->execute([
            $year,$month,
            $_POST['sumTienVe']      ?? '[]',
            (int)($_POST['tienVeSauThue'] ?? 0),
            $manualRevenue,
            (int)($_POST['laiRong']       ?? 0)
        ]);
    
        header('Location: ?action=viewMonthlyHistory');
        exit;
    }
    
    

    private function saveSummaryAndRedirect(int $year, int $month): void
    {
        $sumTienVeJson = $_POST['sumTienVe']      ?? '[]';
        $tienVeSauThue = (int)($_POST['tienVeSauThue'] ?? 0);
        $manualRevenue = (int)($_POST['manualRevenue'] ?? 0);
        $laiRong       = (int)($_POST['laiRong']       ?? 0);
        $actionType    = $_POST['actionType']         ?? 'override';
    
        $old = $this->db->prepare("
            SELECT list_tien_ve_san,tien_ve_sau_thue,manual_revenue,lai_rong
              FROM cost_calc_summary
             WHERE year=? AND month=? LIMIT 1
        ");
        $old->execute([$year,$month]);
        $old = $old->fetch(PDO::FETCH_ASSOC);
    
        if ($actionType==='update' && $old) {
            $sumTienVeJson = json_encode(
                array_merge(json_decode($old['list_tien_ve_san']??'[]',true) ?: [],
                            json_decode($sumTienVeJson,true) ?: []),
                JSON_UNESCAPED_UNICODE
            );
            $tienVeSauThue += (int)$old['tien_ve_sau_thue'];
            $manualRevenue += (int)$old['manual_revenue'];
            $laiRong       += (int)$old['lai_rong'];
        }
    
        $this->db->prepare("DELETE FROM cost_calc_summary WHERE year=? AND month=?")->execute([$year,$month]);
        $this->db->prepare("
            INSERT INTO cost_calc_summary
                   (year,month,list_tien_ve_san,tien_ve_sau_thue,
                    manual_revenue,lai_rong)
            VALUES (:y,:m,:list,:st,:rev,:lr)
        ")->execute([
            ':y'=>$year,':m'=>$month,':list'=>$sumTienVeJson,
            ':st'=>$tienVeSauThue,':rev'=>$manualRevenue,':lr'=>$laiRong
        ]);
    
        header('Location: ?action=viewMonthlyHistory');
        exit;
    }


public function viewMonthlyHistory()
{
    // Lấy danh sách DISTINCT year, month
    $stmt = $this->db->query("
       SELECT DISTINCT year, month 
         FROM cost_calc_history
     ORDER BY year DESC, month DESC
    ");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Gọi view hiển thị, ví dụ: views/list_history.php
    include __DIR__ . '/../Views/list_history.php';
}




public function viewMonthlyData(): void
{
    $year  = (int)($_GET['year'] ?? 0);
    $month = (int)($_GET['month'] ?? 0);

    /* 1. Lấy toàn bộ history */
    $stmt = $this->db->prepare("
        SELECT sku_hang_hoa,quantity,gia_von,tong_von,source
          FROM cost_calc_history
         WHERE year=? AND month=?");
    $stmt->execute([$year,$month]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    /* 2. Tách 4 mảng */
    $matched=$unmatched=$mMatched=$mUnmatched=[];
    foreach($rows as $r){
        $hasPrice = (int)$r['gia_von']>0;
        if ($r['source']==='file')
            $hasPrice? $matched[]=$r   : $unmatched[]=$r;
        else
            $hasPrice? $mMatched[]=$r  : $mUnmatched[]=$r;
    }

    /* 3. Summary */
    $summary = $this->db->prepare("
        SELECT list_tien_ve_san,tien_ve_sau_thue,
               manual_revenue,lai_rong
          FROM cost_calc_summary
         WHERE year=? AND month=? LIMIT 1
    ");
    $summary->execute([$year,$month]);
    $summary = $summary->fetch(PDO::FETCH_ASSOC) ?: [
        'list_tien_ve_san'=>'[]',
        'tien_ve_sau_thue'=>0,
        'manual_revenue'  =>0,
        'lai_rong'        =>0
    ];

    /* 4. Bảng “Danh sách SKU” dùng toàn bộ $rows */
    $data = $rows;

    include __DIR__.'/../Views/monthly_data_view.php';
}




// Hiển thị menu “Đơn thủ công”
public function manualOrders(): void
{
    $tab = $_GET['tab'] ?? '';

    $filter = $_GET['filter'] ?? 'today';


    $where = '';
    switch ($filter) {
        case 'today':
            $where = "WHERE DATE(created_at) = CURDATE()";
            break;
        case '7days':
            $where = "WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            break;
        case 'thismonth':
            $where = "WHERE YEAR(created_at) = YEAR(CURDATE()) 
                      AND MONTH(created_at) = MONTH(CURDATE())";
            break;
        case 'all':
        default:
            // 'all' thì không WHERE
            $where = '';
    }

    // Thực hiện query
    $sql = "SELECT * FROM orders {$where} ORDER BY created_at DESC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Chuẩn bị biến để view hiển thị thông báo nếu rỗng
    $noOrdersMessage = '';
    if (empty($orders)) {
        if ($filter === 'today') {
            $noOrdersMessage = 'Hôm nay chưa có đơn hàng nào';
        } else {
            $noOrdersMessage = 'Không có đơn hàng nào';
        }
    }

    // Gửi thêm $filter và $noOrdersMessage sang view để tiện hiển thị
    include __DIR__ . '/../Views/manual_orders.php';
}


public function createOrder(): void
{
    // 1) Xử lý upload ảnh nếu có
    $imgPath = null;
    if (!empty($_FILES['order_image']['tmp_name'])) {
        $dest = __DIR__ . '/../../public/uploads/';
        if (!is_dir($dest)) {
            mkdir($dest, 0755, true);
        }
        $name = time() . '_' . basename($_FILES['order_image']['name']);
        move_uploaded_file($_FILES['order_image']['tmp_name'], $dest . $name);
        $imgPath = 'uploads/' . $name;
    }

    // 2) Lấy mảng SKU và số lượng
    $skuArr = $_POST['sku_hang_hoa'] ?? [];
    $qtyArr = $_POST['sku_quantity'] ?? [];

    // Trim & lọc
    $items = [];
    foreach ($skuArr as $i => $sku) {
        $sku = trim((string)$sku);
        $qty = (int)($qtyArr[$i] ?? 0);
        // Nếu cả SKU và qty đều rỗng/0 thì bỏ qua
        if ($sku === '' && $qty === 0) {
            continue;
        }
        // Thêm item
        $items[] = ['sku' => $sku, 'qty' => $qty];
    }

    // 3) Kiểm tra: nếu có ảnh thì luôn cho phép, còn không thì phải có ít nhất 1 trường khác
    $hasValue = false;
    if ($imgPath) {
        $hasValue = true;
    } else {
        // như trước: kiểm tra SKU/QTY hoặc các trường còn lại
        if (!empty($items)) {
            $hasValue = true;
        } else {
            $fields = [
                trim((string)($_POST['customer_name'] ?? '')),
                trim((string)($_POST['phone'] ?? '')),
                trim((string)($_POST['address'] ?? '')),
                (int)($_POST['sale_price'] ?? 0),
                (int)($_POST['ship_fee'] ?? 0),
            ];
            foreach ($fields as $v) {
                if (is_string($v) ? $v !== '' : $v !== 0) {
                    $hasValue = true;
                    break;
                }
            }
        }
    }

    if (!$hasValue) {
        $_SESSION['error'] = 'Bạn phải nhập ít nhất 1 thông tin (hoặc tải ảnh) trước khi tạo đơn.';
        header('Location: ?action=manualOrders');
        exit;
    }


    // 4) Chuẩn bị data để lưu
    $data = [
        'order_image'   => $imgPath,
        'customer_name' => trim((string)($_POST['customer_name'] ?? '')),
        'phone'         => trim((string)($_POST['phone'] ?? '')),
        'address'       => trim((string)($_POST['address'] ?? '')),
        // Lưu mảng SKU+qty dưới dạng JSON
        'sku_items'     => json_encode($items, JSON_UNESCAPED_UNICODE),
        'sale_price'    => (int)($_POST['sale_price'] ?? 0),
        'ship_fee'      => (int)($_POST['ship_fee'] ?? 0),
    ];

    // 5) Insert vào bảng orders
    $sql = "INSERT INTO orders 
        (order_image, customer_name, phone, address, sku_items, sale_price, ship_fee)
      VALUES 
        (:img, :name, :phone, :addr, :items, :price, :ship)";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([
        ':img'   => $data['order_image'],
        ':name'  => $data['customer_name'],
        ':phone' => $data['phone'],
        ':addr'  => $data['address'],
        ':items' => $data['sku_items'],
        ':price' => $data['sale_price'],
        ':ship'  => $data['ship_fee'],
    ]);

    // 6) Thông báo và redirect về trang quản lý đơn
    $_SESSION['success'] = 'Tạo đơn thành công!';
    header('Location: ?action=manualOrders&tab=manage');
    exit;
}

public function ajaxSearchSku(): void
{
    $term = trim($_GET['term'] ?? '');
    $out  = [];

    if ($term !== '') {
        // 1. Tách các từ, loại bỏ khoảng trắng thừa
        $words = preg_split('/\s+/', $term);

        // 2. Build câu WHERE: sku_hang_hoa LIKE :w0 AND sku_hang_hoa LIKE :w1 ...
        $whereClauses = [];
        $params       = [];
        foreach ($words as $i => $w) {
            $key = ":w{$i}";
            $whereClauses[] = "sku_gian_hang LIKE {$key}";
            $params[$key] = '%' . $w . '%';
        }
        $whereSql = implode(' AND ', $whereClauses);

        // 3. Prepare & execute
        $sql = "
        SELECT DISTINCT sku_gian_hang
            FROM sku_mapping
        WHERE {$whereSql}
        LIMIT 10
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $out = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    header('Content-Type: application/json');
    echo json_encode($out);
    exit;
}
// Xóa đơn
public function deleteOrder(): void
{
    $id = (int)($_POST['id'] ?? 0);
    if ($id) {
        $stmt = $this->db->prepare("DELETE FROM orders WHERE id=:id");
        $stmt->execute([':id'=>$id]);
    }
    header('Location: ?action=manualOrders&tab=manage');
    exit;
}

// Cập nhật đơn (sửa)
public function updateOrder(): void
{
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) {
        header('Location: ?action=manualOrders&tab=manage');
        exit;
    }

    // Xử lý ảnh mới (nếu có)
    $imgPath = null;
    if (!empty($_FILES['order_image']['tmp_name'][$id])) {
        $tmp = $_FILES['order_image']['tmp_name'][$id];
        $dest = __DIR__ . '/../../public/uploads/';
        if (!is_dir($dest)) mkdir($dest,0755,true);
        $name = time() . "_{$id}_" . basename($_FILES['order_image']['name'][$id]);
        move_uploaded_file($tmp, $dest.$name);
        $imgPath = 'uploads/' . $name;
    }

    // SKU + Qty
    $skuArr = $_POST['sku_hang_hoa'][$id] ?? [];
    $qtyArr = $_POST['sku_quantity'][$id] ?? [];
    $items = [];
    foreach ($skuArr as $i => $sku) {
        $sku = trim((string)$sku);
        $qty = (int)($qtyArr[$i] ?? 0);
        if ($sku !== '' || $qty>0) {
            $items[] = ['sku'=>$sku,'qty'=>$qty];
        }
    }
    $skuJson = json_encode($items, JSON_UNESCAPED_UNICODE);

    // Các field khác
    $name  = trim((string)($_POST['customer_name'][$id] ?? ''));
    $phone = trim((string)($_POST['phone'][$id] ?? ''));
    $addr  = trim((string)($_POST['address'][$id] ?? ''));
    $price = (int)($_POST['sale_price'][$id] ?? 0);
    $ship  = (int)($_POST['ship_fee'][$id] ?? 0);

    // Build SQL
    $sql = "UPDATE orders SET
      customer_name=:name,
      phone=:phone,
      address=:addr,
      sku_items=:items,
      sale_price=:price,
      ship_fee=:ship"
      . ($imgPath ? ", order_image=:img" : "")
      ." WHERE id=:id";
    $stmt = $this->db->prepare($sql);
    $params = [
      ':name'=>$name,':phone'=>$phone,':addr'=>$addr,
      ':items'=>$skuJson,':price'=>$price,':ship'=>$ship,
      ':id'=>$id
    ];
    if ($imgPath) $params[':img']=$imgPath;
    $stmt->execute($params);

    header('Location: ?action=manualOrders&tab=manage');
    exit;
}


/* ================= AJAX: lưu 1 SKU hàng hóa ================ */
public function saveSkuAjax(): void
{
    $sku  = trim((string)($_POST['sku']      ?? ''));
    $gv   = (int)($_POST['gia_von'] ?? 0);
    $qty  = (int)($_POST['ton_kho'] ?? 0);

    if ($sku === '') {
        http_response_code(400);
        echo json_encode(['success'=>false]);
        return;
    }

    $this->updateGiaVon($sku, $gv);
    $this->updateTonKho($sku, $qty);

    header('Content-Type: application/json');
    echo json_encode(['success'=>true]);
}



/* ============ AJAX: xoá 1 SKU hàng hóa + mapping =========== */
public function deleteSkuAjax(): void
{
    $sku = trim((string)($_POST['sku'] ?? ''));
    if ($sku === '') {
        http_response_code(400);
        echo json_encode(['success'=>false]);
        return;
    }

    /* Xoá mapping, giá vốn, tồn kho */
    $this->db->prepare("DELETE FROM sku_mapping WHERE sku_hang_hoa=:sku")
             ->execute([':sku'=>$sku]);
    $this->db->prepare("DELETE FROM gia_von WHERE sku_hang_hoa=:sku")
             ->execute([':sku'=>$sku]);
    $this->db->prepare("DELETE FROM ton_kho WHERE sku_hang_hoa=:sku")
             ->execute([':sku'=>$sku]);

    header('Content-Type: application/json');
    echo json_encode(['success'=>true]);
}






}
