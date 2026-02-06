<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require 'db.php';
require __DIR__ . '/../auth.php';

function format_status_time($datetime) {
    if (!$datetime) return '';
    return date('H:i - d/m/Y', strtotime($datetime));
}

function format_dmy($ymd){
    return $ymd ? date('d/m/Y', strtotime($ymd)) : '';
}

/* ===========================================================================
   (A) XỬ LÝ PHẦN “GIAO VIỆC MỚI” 
   =========================================================================== */
if (isset($_POST['create_task'])) {
    // 1) Lấy dữ liệu từ form
    $taskName       = trim($_POST['task_name']);
    $taskContent    = trim($_POST['task_content']);
    $dueDate        = !empty($_POST['due_date']) ? $_POST['due_date'] : null; // YYYY-mm-dd
    $generalNote    = trim($_POST['general_note']);
    // Mảng user_id được chọn (sinh ra từ ô tìm kiếm -> hidden inputs)
    $assignees      = !empty($_POST['assignees']) ? $_POST['assignees'] : [];
    // Mảng tiến độ công việc (các input progress_content[], progress_note[], progress_due_date[])
    $progressContent = $_POST['progress_content'] ?? [];
    $progressNote    = $_POST['progress_note'] ?? [];
    $progressDue     = $_POST['progress_due_date'] ?? [];
    
    // 2) Tạo record trong bảng tasks
    $stmtTask = $conn->prepare("
        INSERT INTO tasks (task_name, task_content, due_date, general_note, created_by)
        VALUES (:tname, :tcontent, :due, :gnote, :adminID)
    ");
    $stmtTask->execute([
        'tname'    => $taskName,
        'tcontent' => $taskContent,
        'due'      => $dueDate,
        'gnote'    => $generalNote,
        'adminID'  => 1   // hoặc $_SESSION['admin_id'] nếu có lưu ID admin
    ]);
    $newTaskID = $conn->lastInsertId();
    
    // 3) Thêm các user được assign vào bảng task_assignees
    if (!empty($assignees)) {
        $stmtAss = $conn->prepare("
            INSERT INTO task_assignees (task_id, user_id)
            VALUES (:tid, :uid)
        ");
        foreach ($assignees as $uid) {
            $stmtAss->execute([
                'tid' => $newTaskID,
                'uid' => $uid
            ]);
        }
    }
    
    // 4) Thêm các dòng tiến độ (sub-task)
    $stmtProg = $conn->prepare("
        INSERT INTO task_progress (task_id, progress_content, progress_note, due_date)
        VALUES (:tid, :pcontent, :pnote, :pdue)
    ");
    for ($i = 0; $i < count($progressContent); $i++) {
        $pContent = trim($progressContent[$i]);
        if ($pContent === '') continue; // bỏ qua nếu không nhập dữ liệu
        $pNote = trim($progressNote[$i]);
        $pDue  = !empty($progressDue[$i]) ? $progressDue[$i] : null;
        $stmtProg->execute([
            'tid'      => $newTaskID,
            'pcontent' => $pContent,
            'pnote'    => $pNote,
            'pdue'     => $pDue
        ]);
    }
    
    $_SESSION['flash_msg'] = "Đã tạo công việc mới và giao cho các nhân viên thành công!";
    header("Location: giao_viec.php");
    exit;
}
/* =====================================================================
   (B0) XỬ LÝ XÓA TASK
   ===================================================================== */
   if (isset($_POST['delete_task'])) {
    $delTID = (int)$_POST['delete_task'];
    // Xóa tất cả sub-task liên quan
    $conn->prepare("DELETE FROM task_progress  WHERE task_id = :tid")
         ->execute(['tid'=>$delTID]);
    // Xóa tất cả assignees liên quan
    $conn->prepare("DELETE FROM task_assignees WHERE task_id = :tid")
         ->execute(['tid'=>$delTID]);
    // Xóa chính bản ghi task
    $conn->prepare("DELETE FROM tasks          WHERE id       = :tid")
         ->execute(['tid'=>$delTID]);

    $_SESSION['flash_msg'] = "Đã xóa công việc ID $delTID thành công.";
    header("Location: giao_viec.php?tab=quanly");
    exit;
}
/* ===========================================================================
   (B) XỬ LÝ PHẦN “QUẢN LÝ TIẾN ĐỘ” (Cập nhật task)
   =========================================================================== */
if (isset($_POST['update_task'])) {
    // Cập nhật thông tin tổng thể của task
    $taskID      = (int)$_POST['task_id'];
    $taskName    = trim($_POST['task_name']);
    $taskContent = trim($_POST['task_content']);
    $dueDate     = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
    $generalNote = trim($_POST['general_note']);
    
    $stmtUp = $conn->prepare("
        UPDATE tasks
        SET task_name     = :tname,
            task_content  = :tcontent,
            due_date      = :due,
            general_note  = :gnote
        WHERE id          = :tid
    ");
    $stmtUp->execute([
        'tname'    => $taskName,
        'tcontent' => $taskContent,
        'due'      => $dueDate,
        'gnote'    => $generalNote,
        'tid'      => $taskID
    ]);
    
    // Cập nhật các sub-task đã có (mảng update_progress_id[], update_progress_content[], update_progress_note[], update_progress_due[])
    if (!empty($_POST['update_progress_id'])) {
        $stmtUpdateSub = $conn->prepare("
            UPDATE task_progress
            SET progress_content = :pcontent,
                progress_note    = :pnote,
                due_date         = :pdue
            WHERE id = :pid
        ");
        $countSub = count($_POST['update_progress_id']);
        for ($i = 0; $i < $countSub; $i++) {
            $pID    = (int)$_POST['update_progress_id'][$i];
            $pCont  = trim($_POST['update_progress_content'][$i]);
            $pNote  = trim($_POST['update_progress_note'][$i]);
            $pDue   = !empty($_POST['update_progress_due'][$i]) ? $_POST['update_progress_due'][$i] : null;
            $stmtUpdateSub->execute([
                'pcontent' => $pCont,
                'pnote'    => $pNote,
                'pdue'     => $pDue,
                'pid'      => $pID
            ]);
        }
    }
    
    // Thêm mới sub-task (nếu có)
    if (!empty($_POST['new_progress_content'])) {
        $stmtAddSub = $conn->prepare("
            INSERT INTO task_progress (task_id, progress_content, progress_note, due_date)
            VALUES (:tid, :pcontent, :pnote, :pdue)
        ");
        $countNew = count($_POST['new_progress_content']);
        for ($i = 0; $i < $countNew; $i++) {
            $pCont = trim($_POST['new_progress_content'][$i]);
            if ($pCont === '') continue;
            $pNote = trim($_POST['new_progress_note'][$i]);
            $pDue  = !empty($_POST['new_progress_due'][$i]) ? $_POST['new_progress_due'][$i] : null;
            $stmtAddSub->execute([
                'tid'      => $taskID,
                'pcontent' => $pCont,
                'pnote'    => $pNote,
                'pdue'     => $pDue
            ]);
        }
    }
/* =======================================================================
   (B2) XỬ LÝ XÓA TASK
   ======================================================================= */


    // XÓA hết người được giao cũ
    $stmtDelAss = $conn->prepare("DELETE FROM task_assignees WHERE task_id = :tid");
    $stmtDelAss->execute(['tid' => $taskID]);

    // Thêm lại theo assignees[] từ form
    if (!empty($_POST['assignees'])) {
        $stmtAss = $conn->prepare("
            INSERT INTO task_assignees (task_id, user_id)
            VALUES (:tid, :uid)
        ");
        foreach ($_POST['assignees'] as $uid) {
            $stmtAss->execute([
                'tid' => $taskID,
                'uid' => (int)$uid,
            ]);
        }
    }

    $_SESSION['flash_msg'] = "Đã cập nhật công việc thành công!";
    header("Location: giao_viec.php#task-{$taskID}");
    exit;
}


/* ===========================================================================
   (B1) XỬ LÝ XÓA 1 sub-task 
   =========================================================================== */
if (isset($_GET['del_progress_id'])) {
    $delPID = (int)$_GET['del_progress_id'];
    $stmtDel = $conn->prepare("DELETE FROM task_progress WHERE id = :pid LIMIT 1");
    $stmtDel->execute(['pid' => $delPID]);
    
    $_SESSION['flash_msg'] = "Đã xóa tiến độ ID $delPID";
    header("Location: giao_viec.php?tab=quanly");
    exit;
}

/* ===========================================================================
   (C) LẤY DANH SÁCH NHÂN VIÊN cho chức năng tìm kiếm (dùng JS)
   =========================================================================== */
$stmtUsers = $conn->query("SELECT id, username FROM users ORDER BY username");
$allUsers = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

/* ===========================================================================
   (D) LẤY TOÀN BỘ TASKS VÀ SUB-TASK để hiển thị cho admin
   =========================================================================== */
$stmtTasks = $conn->query("
    SELECT t.*,
           (SELECT GROUP_CONCAT(u.username SEPARATOR ', ')
            FROM task_assignees ta
            JOIN users u ON ta.user_id = u.id
            WHERE ta.task_id = t.id
           ) AS assignees
    FROM tasks t
    ORDER BY t.id DESC
");
$allTasks = $stmtTasks->fetchAll(PDO::FETCH_ASSOC);

// Phân tách tasks theo trạng thái: pending (chưa hoàn thành) và completed (đã hoàn thành)
$pendingTasks = [];
$completedTasks = [];
foreach ($allTasks as $task) {
    if (empty($task['completed_at'])) {
        $pendingTasks[] = $task;
    } else {
        $completedTasks[] = $task;
    }
}

// LẤY TOÀN BỘ SUB-TASK từ bảng task_progress
$stmtAllSub = $conn->query("SELECT * FROM task_progress ORDER BY id ASC");
$allSubTasks = $stmtAllSub->fetchAll(PDO::FETCH_ASSOC);

// Gom các sub-task theo task_id vào mảng $subTasksByTask
$subTasksByTask = [];
foreach ($allSubTasks as $st) {
    $subTasksByTask[$st['task_id']][] = $st;
}


$newTasksForPopup = [];
foreach ($completedTasks as $t) {
    // Giả sử nếu cột admin_popup_shown chưa được thiết lập hoặc = 0 thì chưa thông báo
    if (!isset($t['admin_popup_shown']) || $t['admin_popup_shown'] == 0) {
        $newTasksForPopup[] = $t;
    }
}
if (!empty($newTasksForPopup)) {
    // Lấy danh sách id, cập nhật admin_popup_shown = 1 để không popup lại lần sau
    $ids = array_column($newTasksForPopup, 'id');
    $in = implode(',', array_map('intval', $ids));
    $conn->query("UPDATE tasks SET admin_popup_shown = 1 WHERE id IN ($in)");
}
$newTasksForPopup = [];
foreach ($completedTasks as $t) {
    if (!isset($t['admin_popup_shown']) || $t['admin_popup_shown'] == 0) {
        $newTasksForPopup[] = $t;
    }
}
if (!empty($newTasksForPopup)) {
    $ids = array_column($newTasksForPopup, 'id');
    $in = implode(',', array_map('intval', $ids));
    $conn->query("UPDATE tasks SET admin_popup_shown = 1 WHERE id IN ($in)");
}
// (C1) Xây map id→username để hiển thị tag
$allUsersMap = [];
foreach ($allUsers as $u) {
  $allUsersMap[$u['id']] = $u['username'];
}

// (C2) Lấy hết task_assignees thành mảng $assigneesByTaskId
$stmtTa = $conn->query("SELECT task_id, user_id FROM task_assignees");
$allTa  = $stmtTa->fetchAll(PDO::FETCH_ASSOC);
$assigneesByTaskId = [];
foreach ($allTa as $ta) {
  $assigneesByTaskId[$ta['task_id']][] = $ta['user_id'];
}

?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Giao Việc & Quản Lý Tiến Độ</title>
    <!-- Choices.js CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link rel="stylesheet" href="/logoday.css">


    <!-- Choices.js JS -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <link rel="stylesheet"
          href="giao_viec.css?v=<?= filemtime(__DIR__ . '/giao_viec.css') ?>">
</head>
<body>


<div class="container-body">
<?php include $_SERVER['DOCUMENT_ROOT'] . '/logoday.php'; ?>

    <?php
    if (isset($_SESSION['flash_msg'])) {
        echo "<div class='flash-msg'>".$_SESSION['flash_msg']."</div>";
        unset($_SESSION['flash_msg']);
    }
    ?>
    
    <h1>Trang Giao Việc (Admin)</h1>

    <!-- Các nút điều hướng giữa 3 phần -->
    <div class="tabs">
    <div class="tab-btn active" data-tab="tab-create">Giao Việc Mới</div>
    <div class="tab-btn" data-tab="tab-pending">Công việc đang giao</div>
    <div class="tab-btn" data-tab="tab-completed">Công việc đã hoàn thành</div>
    </div>

    <!-- Tab 1: Giao Việc Mới -->
    <div id="tab-create" class="tab-content active">
        <h2>1) Giao Việc Mới</h2>
        <form method="POST" action="">
            <p><strong>Tên công việc:</strong></p>
            <input type="text" name="task_name" required>
            
            <p><strong>Nội dung chính công việc:</strong></p>
            <textarea name="task_content" rows="3" cols="60" required></textarea>
            
            <div class="assign-date-row">
                <div class="assign-col">
                    <p><strong>Chọn nhân viên để giao việc:</strong></p>
                    <div class="employee-search-container">
                    <input type="text" id="employeeSearch" placeholder="Nhập username..." autocomplete="off">
                    <div id="employeeSuggestions"></div>
                    </div>
                    <div id="selectedEmployees" style="margin-top:10px;"></div>
                </div>

                <div class="duedate-col">
                    <p><strong>Hoàn thành trước ngày:</strong></p>
                    <input type="date" name="due_date">
                </div>
            </div>
            <h4 style="margin-top:1.2rem;">Tiến độ công việc:</h4>
                <div class="progress-grid header">
                <span>Tiến độ</span>
                <span>Ghi chú</span>
                <span>Thời gian hoàn thành</span>
            </div>
            <div id="progressContainer">
                <div class="progress-grid">
                    <textarea name="progress_content[]" rows="1" placeholder="Tiến độ 1"></textarea>
                    <textarea name="progress_note[]" rows="1" placeholder="Ghi chú tiến độ 1"></textarea>
                    <input type="date" name="progress_due_date[]" placeholder="Hạn tiến độ 1">
                </div>
                <div class="progress-grid">
                    <textarea name="progress_content[]" rows="1" placeholder="Tiến độ 2"></textarea>
                    <textarea name="progress_note[]" rows="1" placeholder="Ghi chú tiến độ 2"></textarea>
                    <input type="date" name="progress_due_date[]" placeholder="Hạn tiến độ 2">
                </div>
            </div>
            
            <p><strong>Ghi chú tổng:</strong></p>
            <textarea name="general_note" rows="2" cols="60"></textarea>
            
            <br><br>
            <button type="submit" name="create_task">Giao Việc</button>
        </form>
    </div>

    <!-- Tab 2: Công việc đang giao -->
    <div id="tab-pending" class="tab-content">
    <h2 class="section-title">2) Công việc đang giao (chưa hoàn thành)</h2>

    <!-- Khu vực lọc -->
    <div class="filter-section">
        <div class="pending-filter-wrapper">
    <label class="loc-nhan-vien" for="filterInputPending">Lọc nhân viên: </label>

    <!-- Container để bọc input và suggestions -->
    <div class="pending-input-container">
        <input type="text" id="filterInputPending" placeholder="Nhập username..." autocomplete="off">
        <div id="filterSuggestionsPending" class="filter-suggestions"></div>
    </div>

    <!-- Nơi hiển thị danh sách username đã chọn -->
    <div id="selectedFiltersPending"></div>
    </div>

    <!-- Giữ nguyên phần Hiển thị: ... -->
    <div class="hien-thi-so-trang">
        <label class="hien-thi" for="tasksPerPagePending" style="margin-left:10px;">Hiển thị:</label>
        <select id="tasksPerPagePending">
            <option value="5">5</option>
            <option value="10" selected>10</option>
            <option value="30">30</option>
            <option value="50">50</option>
            <option value="all">All</option>
        </select>
    </div>
        </div>

    <div class="tasks-container" id="tasksContainerPending">
        <?php if (empty($pendingTasks)): ?>
            <p>Chưa có công việc nào đang giao.</p>
        <?php else: ?>
            <?php foreach ($pendingTasks as $t): ?>
            <div class="task-pending-box" id="task-<?= $t['id'] ?>">
                <form method="POST" action="">
                <!-- ID để biết đang cập nhật công việc nào -->
                <input type="hidden" name="task_id" value="<?= $t['id'] ?>">

                <!-- Tên công việc (có thể sửa) -->
                <div class="form-group">
                    <label><strong>Tên công việc:</strong></label>
                    <input
                    type="text"
                    name="task_name"
                    class="form-control"
                    value="<?= htmlspecialchars($t['task_name']) ?>"
                    required
                    >
                </div>

                <!-- Giao cho (có thể xóa/thêm user) -->
                <div class="form-group">
                    <label><strong>Giao cho:</strong></label>
                    <div class="assignees-edit">
                    <?php foreach ($assigneesByTaskId[$t['id']] ?? [] as $uid): ?>
                        <span class="employee-tag" data-id="<?= $uid ?>">
                        <?= htmlspecialchars($allUsersMap[$uid]) ?>
                        <input type="hidden" name="assignees[]" value="<?= $uid ?>">
                        &times;
                        </span>
                    <?php endforeach; ?>
                    <button type="button" class="btn-add-assignee">+</button>
                    <div class="employee-search-container pending" style="display:none">
                        <input type="text" class="employeeSearchPending" placeholder="Nhập username...">
                        <div class="employeeSuggestionsPending"></div>
                    </div>
                    </div>
                </div>

                <!-- Nội dung chính (textarea để sửa) -->
                <div class="form-group">
                    <label><strong>Nội dung chính:</strong></label>
                    <textarea
                    name="task_content"
                    rows="2"
                    class="form-control"
                    required
                    ><?= htmlspecialchars($t['task_content']) ?></textarea>
                </div>

                <!-- Hạn hoàn thành tổng -->
                <div class="form-group">
                    <label><strong>Hoàn thành trước:</strong></label>
                    <input
                    type="date"
                    name="due_date"
                    class="form-control"
                    value="<?= $t['due_date'] ?>"
                    >
                </div>

                <!-- Ghi chú tổng -->
                <div class="form-group">
                    <label><strong>Ghi chú tổng:</strong></label>
                    <textarea
                    name="general_note"
                    rows="2"
                    class="form-control"
                    ><?= htmlspecialchars($t['general_note']) ?></textarea>
                </div>

                <!-- Tiến độ con -->
                <h4>Các tiến độ:</h4>
                <div class="subtask-table-container">
                    <table class="subtask-table subtask-table-pending">
                    <thead>
                        <tr>
                        <th>STT</th>
                        <th>Nội dung</th>
                        <th>Ghi chú</th>
                        <th>Hạn</th>
                        <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        if (!empty($subTasksByTask[$t['id']])) :
                            foreach ($subTasksByTask[$t['id']] as $sb) :
                        ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td>
                            <textarea name="update_progress_content[]" rows="1"><?= htmlspecialchars($sb['progress_content']) ?></textarea>
                            </td>
                            <td>
                            <textarea name="update_progress_note[]" rows="1"><?= htmlspecialchars($sb['progress_note']) ?></textarea>
                            </td>
                            <td>
                            <input type="date" name="update_progress_due[]" value="<?= $sb['due_date'] ?>">
                            </td>
                            <td>
                            <?php if ($sb['is_completed']): ?>
                                Đã xong lúc <?= format_status_time($sb['completed_at']) ?>
                            <?php else: ?>
                                <form method="POST" style="display:inline" onsubmit="return confirm('Xác nhận hoàn thành tiến độ này?');">
                                <input type="hidden" name="progress_id" value="<?= $sb['id'] ?>">
                                </form>
                            <?php endif; ?>
                            </td>
                        </tr>
                        <input type="hidden" name="update_progress_id[]" value="<?= $sb['id'] ?>">
                        <?php
                            endforeach;
                        else:
                        ?>
                        <tr>
                            <td colspan="5"><em>Chưa có tiến độ</em></td>
                        </tr>
                        <?php endif; ?>

                        <!-- Hàng để thêm sub-task mới -->
                        <tr class="new-progress-row">
                        <td>+</td>
                        <td><textarea name="new_progress_content[]" rows="1" placeholder="Tiến độ mới"></textarea></td>
                        <td><textarea name="new_progress_note[]" rows="1" placeholder="Ghi chú mới"></textarea></td>
                        <td><input type="date" name="new_progress_due[]"></td>
                        <td></td>
                        </tr>
                    </tbody>
                    </table>
                </div>

                <!-- Nút lưu -->
                <div class="form-group nut-luu-xoa">
                    <button type="submit" name="update_task" class="btn btn-primary">
                    Lưu cập nhật
                    </button>
                    <!-- Nút Xóa công việc -->
                    <button 
                        type="submit" 
                        name="delete_task" 
                        value="<?= $t['id'] ?>" 
                        onclick="return confirm('Bạn có chắc muốn xóa toàn bộ công việc này?');"
                        class="btn btn-danger"
                        >
                        Xóa công việc
                    </button>
                </div>
                
                </form>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>


    <div class="pagination-controls" id="paginationPending"></div>
    </div>


    <!-- Tab 3: Công việc đã hoàn thành -->
    <div id="tab-completed" class="tab-content">
        <h2 class="section-title">3) Công việc đã hoàn thành</h2>
        <div class="filter-section">

  <!-- Hàng 1 : Lọc nhân viên -->
  <div class="completed-filter-wrapper">
      <label class="loc-nhan-vien" for="filterInputCompleted">Lọc nhân viên:</label>

      <div class="completed-input-container">
          <input  type="text" id="filterInputCompleted" placeholder="Nhập username…" autocomplete="off">
          <div id="filterSuggestionsCompleted" class="filter-suggestions"></div>
      </div>

      <div id="selectedFiltersCompleted"></div>
  </div>

  <!-- Hàng 2 : Hiển thị -->
  <div class="hien-thi-so-trang-completed">
      <label class="hien-thi" for="tasksPerPageCompleted">Hiển thị:</label>
      <select id="tasksPerPageCompleted">
          <option value="5">5</option>
          <option value="10" selected>10</option>
          <option value="30">30</option>
          <option value="50">50</option>
          <option value="all">All</option>
      </select>
  </div>

</div>
<div class="tasks-container" id="tasksContainerCompleted">
    <?php if (empty($completedTasks)): ?>
      <p>Chưa có công việc hoàn thành.</p>
    <?php else: ?>
      <?php foreach ($completedTasks as $t): ?>
        <div class="task-completed-box" id="task-<?= $t['id'] ?>">
          
          

          <!-- Thông tin công việc -->
          <h3>Công việc: "<?= htmlspecialchars($t['task_name']) ?>"</h3>
          <div class="noidungchinhvagiaocho">
            <p class="two-col">
              <strong>Giao cho:</strong>
              <span class="assignees-text"><?= htmlspecialchars($t['assignees']) ?></span>
            </p>
            <p class="two-col main-content">
              <strong>Nội dung chính:</strong><br>
              <?= nl2br(htmlspecialchars($t['task_content'])) ?>
            </p>
          </div>
          <p><strong>Hoàn thành trước:</strong> <?= format_dmy($t['due_date']) ?></p>
          <p><strong>Ghi chú tổng:</strong><br><?= nl2br(htmlspecialchars($t['general_note'])) ?></p>
          <p><strong>Hoàn thành lúc:</strong> <?= format_status_time($t['completed_at']) ?></p>
          <p><strong>Kết quả Deadline:</strong> <?= htmlspecialchars($t['completion_log']) ?></p>

          <h4>Các tiến độ chi tiết:</h4>
          <div class="subtask-table-container">
            <table class="subtask-table">
              <thead>
                <tr>
                  <th>STT</th>
                  <th>Nội dung</th>
                  <th>Ghi chú</th>
                  <th>Hạn</th>
                  <th>Trạng thái</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $i = 1;
                  if (!empty($subTasksByTask[$t['id']])):
                    foreach ($subTasksByTask[$t['id']] as $sb):
                ?>
                <tr>
                  <td><?= $i++ ?></td>
                  <td><?= nl2br(htmlspecialchars($sb['progress_content'])) ?></td>
                  <td><?= htmlspecialchars($sb['progress_note']) ?></td>
                  <td><?= format_dmy($sb['due_date']) ?></td>
                  <td>
                    <?= $sb['is_completed']
                        ? "Đã xong lúc " . format_status_time($sb['completed_at'])
                        : "Chưa hoàn thành" ?>
                  </td>
                </tr>
                <?php
                    endforeach;
                  else:
                ?>
                <tr>
                  <td colspan="5"><em>Không có tiến độ</em></td>
                </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          <!-- FORM XÓA -->
          <form class="nut-xoa-trang-3" method="POST"
                onsubmit="return confirm('Bạn có chắc muốn xóa toàn bộ công việc này?');"
                >
            <input type="hidden" name="delete_task" value="<?= $t['id'] ?>">
            <button class="btn btn-danger">Xóa công việc</button>
          </form>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
    
  </div>

  <div class="pagination-controls" id="paginationCompleted"></div>
</div>

    <!-- Popup -->
    <?php if (!empty($popupMsg)): ?>
        <div id="popupOverlay" class="popup-overlay">
            <div id="popupBox" class="popup-box">
                <p><?php echo htmlspecialchars($popupMsg); ?></p>
                <button class="close-popup" onclick="closePopup()">Đóng</button>
            </div>
        </div>
    <?php endif; ?>

</div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    // -------------------------
    // CÁC BIẾN CHUNG
    // -------------------------
    // Mảng nhân viên dùng cho cả chức năng tìm kiếm giao việc và lọc trong pending
    var employees = <?php echo json_encode($allUsers); ?>;
    // Đối với ô giao việc (assign) đã chọn
    var selectedEmployees = {};
    // Biến lưu danh sách filter nhân viên trong mục "Lọc nhân viên" (tab pending)
    var selectedFiltersPending = [];
    // -------------------------
    // CHUYỂN ĐỔI TAB
    // -------------------------
    document.querySelectorAll('.tab-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        // xóa active cũ
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        // set active mới
        btn.classList.add('active');
        document.getElementById(btn.dataset.tab).classList.add('active');

        // cập nhật URL hash để nhớ tab này
        history.replaceState(null, '', '#'+btn.dataset.tab);

        // phân trang lại nếu cần
        if (btn.dataset.tab == "tab-pending") {
        paginateTasks("#tasksContainerPending", $("#tasksPerPagePending").val(), "paginationPending");
        } else if (btn.dataset.tab == "tab-completed") {
        paginateTasks("#tasksContainerCompleted", $("#tasksPerPageCompleted").val(), "paginationCompleted");
        }
    });
    });
    // on page load: nếu có hash thì active đúng tab, ngược lại mặc định giữ nguyên (tab-create)
    document.addEventListener('DOMContentLoaded', function() {
    // what’s after the “#”
    const hash = location.hash.slice(1);

    // chọn tab mặc định
    let selTab = 'tab-create';

    // nếu hash là tên tab (tab-pending / tab-completed) thì dùng luôn
    if (hash.startsWith('tab-')) {
        selTab = hash;
    }
    // nếu hash là task-xxx thì auto bật tab-pending
    else if (hash.startsWith('task-')) {
        selTab = 'tab-pending';
    }

    // clear cũ → bật mới
    document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c=>c.classList.remove('active'));
    document.querySelector(`.tab-btn[data-tab="${selTab}"]`).classList.add('active');
    document.getElementById(selTab).classList.add('active');

    // nếu là pending/completed thì gọi paginate lại
    if (selTab==='tab-pending') {
        paginateTasks("#tasksContainerPending", $("#tasksPerPagePending").val(), "paginationPending");
    } else if (selTab==='tab-completed') {
        paginateTasks("#tasksContainerCompleted", $("#tasksPerPageCompleted").val(), "paginationCompleted");
    }

    // nếu là task-xxx thì scroll tới element đó
    if (hash.startsWith('task-')) {
        const el = document.getElementById(hash);
        if (el) el.scrollIntoView({behavior:'smooth', block:'start'});
    }
    });


    // -------------------------
    // POPUP HOÀN THÀNH TASK (ADMIN)
    // -------------------------
    var newCompletedTasks = <?php echo json_encode($newTasksForPopup); ?>;
    function showNextPopup(index) {
    if (index >= newCompletedTasks.length) return;
    var task = newCompletedTasks[index];
    var popupHtml = '<div id="popupOverlay" style="position: fixed; top:0; left:0; right:0; bottom:0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center;">' +
        '<div id="popupBox" style="background: #fff; padding: 20px; border-radius: 5px; text-align: center;">' +
        '<p><strong>' + task.task_name + '</strong> của ' + task.assignees + ' đã hoàn thành ' + task.completion_log + '.</p>' +
        '<button onclick="closePopup()">Đóng</button>' +
        '</div></div>';
    document.body.insertAdjacentHTML('beforeend', popupHtml);
    window.currentPopupIndex = index;
    }
    function closePopup() {
    var overlay = document.getElementById('popupOverlay');
    if(overlay) overlay.remove();
    var nextIndex = (window.currentPopupIndex || 0) + 1;
    showNextPopup(nextIndex);
    }
    document.addEventListener("DOMContentLoaded", function() {
    if (newCompletedTasks.length > 0) {
        showNextPopup(0);
    }
    });

    // -------------------------
    // PHÂN TRANG (PAGINATION)
    // -------------------------
    var currentPagePending = 1;
    var currentPageCompleted = 1;
    function paginateTasks(containerSelector, tasksPerPage, paginationContainerID) {
        var container = $(containerSelector);
        var tasks = container.children(".task-pending-box, .task-completed-box");
        tasks.each(function(index) {
            $(this).attr("data-index", index + 1);
        });
        if(tasksPerPage === "all") {
            tasks.show();
            $("#" + paginationContainerID).empty();
            return;
        }
        tasksPerPage = parseInt(tasksPerPage);
        var currentPage = (containerSelector === "#tasksContainerPending") ? currentPagePending : currentPageCompleted;
        tasks.hide();
        var start = (currentPage - 1) * tasksPerPage;
        tasks.slice(start, start + tasksPerPage).show();

        // Setup pagination controls
        var totalTasks = tasks.length;
        var totalPages = Math.ceil(totalTasks/tasksPerPage);
        var pagDiv = $("#" + paginationContainerID);
        pagDiv.empty();
        if(totalPages > 1){
            if(currentPage > 1){
                pagDiv.append('<button onclick="changePage(\''+containerSelector+'\', '+(currentPage-1)+', \''+paginationContainerID+'\')">Trước</button>');
            }
            pagDiv.append(' Page ' + currentPage + ' of ' + totalPages + ' ');
            if(currentPage < totalPages){
                pagDiv.append('<button onclick="changePage(\''+containerSelector+'\', '+(currentPage+1)+', \''+paginationContainerID+'\')">Sau</button>');
            }
        }
    }
    function changePage(containerSelector, newPage, paginationContainerID){
        if(containerSelector === "#tasksContainerPending"){
            currentPagePending = newPage;
            paginateTasks(containerSelector, $("#tasksPerPagePending").val(), paginationContainerID);
        } else {
            currentPageCompleted = newPage;
            paginateTasks(containerSelector, $("#tasksPerPageCompleted").val(), paginationContainerID);
        }
        window.scrollTo(0, 0);
    }
    $("#tasksPerPagePending").on("change", function(){
        currentPagePending = 1;
        paginateTasks("#tasksContainerPending", $(this).val(), "paginationPending");
        window.scrollTo(0, 0);
    });
    $("#tasksPerPageCompleted").on("change", function(){
        currentPageCompleted = 1;
        paginateTasks("#tasksContainerCompleted", $(this).val(), "paginationCompleted");
        window.scrollTo(0, 0);
    });
    $(document).ready(function(){
        paginateTasks("#tasksContainerPending", $("#tasksPerPagePending").val(), "paginationPending");
        paginateTasks("#tasksContainerCompleted", $("#tasksPerPageCompleted").val(), "paginationCompleted");
    });

    // -------------------------
// FILTER NHÂN VIÊN Ở TAB PENDING
// -------------------------
var selectedFiltersPending = [];

// 1) Gợi ý khi gõ
function renderFilterSuggestionsPending(query) {
  const suggestionsDiv = $("#filterSuggestionsPending");
  suggestionsDiv.empty();
  if (!query.trim()) {
    suggestionsDiv.hide();
    return;
  }
  const filtered = employees.filter(emp =>
    emp.username.toLowerCase().includes(query.toLowerCase()) &&
    !selectedFiltersPending.includes(emp.username)
  );
  if (!filtered.length) {
    suggestionsDiv.hide();
    return;
  }
  filtered.forEach(emp => {
    suggestionsDiv.append(
      `<div class="suggestion-item" data-username="${emp.username}" style="padding:5px;cursor:pointer;">
         ${emp.username}
       </div>`
    );
  });
  suggestionsDiv.show();
}

// 2) Filter các task trên trang
function filterTasksPending() {
  $("#tasksContainerPending .task-pending-box").each(function() {
    // Lấy tất cả username đã giao
    const assignees = $(this)
      .find(".assignees-edit .employee-tag")
      .map(function() {
        return $(this).text().replace("×", "").trim().toLowerCase();
      })
      .get();

    // Show nếu mọi filter đang chọn đều nằm trong assignees
    const ok = selectedFiltersPending.every(f =>
      assignees.includes(f.toLowerCase())
    );
    $(this).toggle(ok);
  });
}

$(function() {
  // Khi gõ vào ô lọc
  $("#filterInputPending").on("input", function() {
    renderFilterSuggestionsPending($(this).val());
  });

  // Khi chọn 1 suggestion
  $(document).on("click", "#filterSuggestionsPending .suggestion-item", function() {
    const uname = $(this).data("username");
    selectedFiltersPending.push(uname);
    $("#selectedFiltersPending").append(
      `<div class="filter-tag" data-username="${uname}"
            style="margin:2px;padding:2px 5px;border:1px solid #aaa;
                   background:#eef;display:inline-block;cursor:pointer;">
         ${uname} &times;
       </div>`
    );
    $("#filterInputPending").val("");
    $("#filterSuggestionsPending").hide();
    filterTasksPending();
  });

  // Khi nhấn vào tag để bỏ filter
  $(document).on("click", "#selectedFiltersPending .filter-tag", function() {
    const uname = $(this).data("username");
    selectedFiltersPending = selectedFiltersPending.filter(u => u !== uname);
    $(this).remove();
    filterTasksPending();
  });

  // Ẩn gợi ý khi click ra ngoài
  $(document).click(function(e) {
    if (!$(e.target).closest("#filterInputPending, #filterSuggestionsPending").length) {
      $("#filterSuggestionsPending").hide();
    }
  });
});

    // -------------------------
    // HÀM THÊM DÒNG SUB-TASK MỚI
    // -------------------------
    /* ---------- TAB‑2: nút “+ Thêm các tiến độ mới” ---------- */


/* Khi người dùng gõ vào ô “Tiến độ” cuối cùng → tự sinh dòng mới */
// $(document).on(
//   'input',
//   'table.subtask-table textarea[name="new_progress_content[]"]',
//   function () {
//     let $tr    = $(this).closest('tr');
//     let $table = $tr.closest('table.subtask-table');
//     if ($tr.is($table.find('tr').last()) && $(this).val().trim() !== '') {
//       appendProgressRow();   // ← đổi từ addSubtaskRow() sang appendProgressRow()
//     }
// });
// --- thêm dòng tiến độ chỉ cho bảng pending ---
$(document).on(
  'input',
  'table.subtask-table-pending textarea[name="new_progress_content[]"]',
  function () {
    const $tr = $(this).closest('tr');
    const $tbl = $tr.closest('table.subtask-table-pending');
    // nếu là hàng cuối và textarea không rỗng thì clone
    if ($tr.is($tbl.find('tr').last()) && $(this).val().trim() !== '') {
      const $new = $tr.clone();
      // xoá hết giá trị cũ
      $new.find('textarea, input').val('');
      $tbl.append($new);
    }
  }
);



    // -------------------------
    // TÌM KIẾM NHÂN VIÊN (CHO GIAO VIỆC MỚI)
    // -------------------------
    function renderSuggestions(query) {
        var suggestionsDiv = $("#employeeSuggestions");
        suggestionsDiv.empty();
        if (query.trim() === "") {
            suggestionsDiv.hide();
            return;
        }
        var filtered = employees.filter(function(emp) {
            return emp.username.toLowerCase().indexOf(query.toLowerCase()) !== -1;
        });
        if (filtered.length === 0) {
            suggestionsDiv.hide();
            return;
        }
        filtered.forEach(function(emp) {
            if (selectedEmployees[emp.id]) return;
            suggestionsDiv.append("<div class='suggestion-item' data-id='" + emp.id + "' style='padding:5px; cursor:pointer;'>" + emp.username + "</div>");
        });
        suggestionsDiv.show();
    }
    $(document).ready(function() {
        $("#employeeSearch").on("keyup", function() {
            var query = $(this).val();
            renderSuggestions(query);
        });
        $(document).on("click", ".suggestion-item", function() {
            var empId = $(this).data("id");
            var empName = $(this).text();
            if (!selectedEmployees[empId]) {
                selectedEmployees[empId] = empName;
                $("#selectedEmployees").append("<div class='employee-tag' data-id='" + empId + "' style='display:inline-block; margin:2px; padding:2px 5px; border:1px solid #aaa; background:#eef; cursor:pointer;'>" + empName + " &times;<input type='hidden' name='assignees[]' value='" + empId + "'></div>");
            }
            $("#employeeSearch").val("");
            $("#employeeSuggestions").hide();
        });
        $(document).on("click", ".employee-tag", function() {
            var empId = $(this).data("id");
            delete selectedEmployees[empId];
            $(this).remove();
        });
        $(document).click(function(event) {
            if (!$(event.target).closest("#employeeSearch, #employeeSuggestions").length) {
                $("#employeeSuggestions").hide();
            }
        });
    });
    document.addEventListener("DOMContentLoaded", function () {
        const tasksPerPageSelect = document.getElementById('tasksPerPagePending');
        if (tasksPerPageSelect) {
            const choices = new Choices(tasksPerPageSelect, {
                searchEnabled: false,     // Tắt thanh tìm kiếm trong dropdown
                itemSelectText: '',       // Ẩn chữ "Press to select"
                shouldSort: false         // Giữ nguyên thứ tự option
            });
        }
    });
    // -------------------------
    // FILTER NHÂN VIÊN Ở TAB COMPLETED
    // -------------------------
    var selectedFiltersCompleted = [];

    function renderFilterSuggestionsCompleted(query) {
        var suggestionsDiv = $("#filterSuggestionsCompleted");
        suggestionsDiv.empty();
        if (query.trim() === "") { suggestionsDiv.hide(); return; }

        var filtered = employees.filter(function(emp){
            return emp.username.toLowerCase().indexOf(query.toLowerCase()) !== -1;
        });

        if (filtered.length === 0) { suggestionsDiv.hide(); return; }

        filtered.forEach(function(emp){
            if (selectedFiltersCompleted.indexOf(emp.username) !== -1) return;      // đã chọn rồi thì bỏ
            suggestionsDiv.append(
                "<div class='suggestion-item' data-username='"+emp.username+"' "+
                "style='padding:5px;cursor:pointer;'>"+emp.username+"</div>"
            );
        });
        suggestionsDiv.show();
    }

    function filterTasksCompleted() {
        $("#tasksContainerCompleted .task-completed-box").each(function(){
            var assigneesText = $(this).find(".assignees-text").text().toLowerCase();
            var show = true;
            selectedFiltersCompleted.forEach(function(f){
                if (assigneesText.indexOf(f.toLowerCase()) === -1) show = false;
            });
            $(this).toggle(show);
        });
    }

    $(document).ready(function(){

        // gõ vào ô tìm kiếm Completed
        $("#filterInputCompleted").on("keyup", function(){
            renderFilterSuggestionsCompleted($(this).val());
        });

        // bấm chọn 1 suggestion
        $(document).on("click", "#filterSuggestionsCompleted .suggestion-item", function(){
            var uname = $(this).data("username");
            if (selectedFiltersCompleted.indexOf(uname) === -1) {
                selectedFiltersCompleted.push(uname);
                $("#selectedFiltersCompleted").append(
                    "<div class='filter-tag' data-username='"+uname+"' "+
                    "style='display:inline-block;margin:2px;padding:2px 5px;border:1px solid #aaa;background:#eef;cursor:pointer;'>"+
                    uname+" &times;</div>"
                );
                filterTasksCompleted();
            }
            $("#filterInputCompleted").val("");
            $("#filterSuggestionsCompleted").hide();
        });

        // bấm vào thẻ đã chọn để gỡ bỏ
        $(document).on("click", "#selectedFiltersCompleted .filter-tag", function(){
            var uname = $(this).data("username");
            selectedFiltersCompleted = selectedFiltersCompleted.filter(function(x){ return x !== uname; });
            $(this).remove();
            filterTasksCompleted();
        });

        // click ra ngoài thì ẩn gợi ý
        $(document).click(function(e){
            if(!$(e.target).closest("#filterInputCompleted, #filterSuggestionsCompleted").length){
                $("#filterSuggestionsCompleted").hide();
            }
        });
    });
    document.addEventListener("DOMContentLoaded", () => {
    const sel = document.getElementById('tasksPerPageCompleted');
    if (sel) new Choices(sel, { searchEnabled:false, itemSelectText:'', shouldSort:false });
    });
    function appendProgressRow() {
  const idx = $("#progressContainer .progress-grid").length + 1;
  $("#progressContainer").append(
    $("<div>", { class:"progress-grid" }).html(
      `<textarea name="progress_content[]" rows="1" placeholder="Tiến độ ${idx}"></textarea>
       <textarea name="progress_note[]" rows="1"        placeholder="Ghi chú tiến độ ${idx}"></textarea>
       <input   type="date"  name="progress_due_date[]" placeholder="Hạn tiến độ ${idx}">`
    )
  );
}

/* Khi gõ ở textarea của hàng cuối cùng → tự thêm hàng mới */
$(document).on("input",
  "#progressContainer .progress-grid:last-child textarea[name='progress_content[]']",
  function () {
    if ($(this).val().trim() !== "") appendProgressRow();
  }
);
/* rút gọn + gắn nút */
function makeExpandable(scope){
  $(scope)
    .find('.task-pending-box, .task-completed-box')
    .each(function(){
        $(this).find('p').each(function(){
            const label = $(this).find('strong').text().trim().toLowerCase();
            if(label.startsWith('nội dung chính') || label.startsWith('ghi chú tổng')){
                if($(this).text().trim().length > 140){        // chỉ khi dài
                    $(this).addClass('expandable collapsed');
                    $('<span class="show-more">Xem thêm</span>')
                      .insertAfter(this);                      // ⬅️  Đặt bên **ngoài** <p>
                }
            }
        });
    });
}

/* toggle – chỉ 1 lần đăng ký */
$(document).on('click', '.show-more', function(){
    const $exp = $(this).prev('.expandable');                 // p đứng ngay trước span
    $exp.toggleClass('collapsed');
    $(this).text($exp.hasClass('collapsed') ? 'Xem thêm' : 'Thu gọn');
});

/* chạy lúc load (và sau khi ajax thêm task, nếu có) */
$(document).ready(function(){
  makeExpandable('#tab-pending');
  makeExpandable('#tab-completed');
});

// Dữ liệu users từ PHP
var employees = <?php echo json_encode($allUsers); ?>;

// 1) Hiển thị/ẩn input tìm
$(document).on('click', '.btn-add-assignee', function(){
  var $cont = $(this).siblings('.employee-search-container.pending');
  $cont.toggle();
  $cont.find('input').focus();
});

// 2) Gợi ý khi gõ
$(document).on('input', '.employeeSearchPending', function(){
  var $list = $(this).siblings('.employeeSuggestionsPending');
  var q = $(this).val().toLowerCase();
  $list.empty();
  if (!q) return $list.hide();
  employees.forEach(function(emp){
    if (emp.username.toLowerCase().includes(q)) {
      // tránh trùng
      if ($(this).closest('form').find('input[value="'+emp.id+'"]').length) return;
      $list.append(
        `<div class="suggestion" data-id="${emp.id}">${emp.username}</div>`
      );
    }
  }.bind(this));
  $list.show();
});

// 3) Chọn đề nghị
$(document).on('click', '.employeeSuggestionsPending .suggestion', function(){
  var id = $(this).data('id'),
      name = $(this).text(),
      $form = $(this).closest('form');

  // thêm tag
  $form.find('.assignees-edit').prepend(
    `<span class="employee-tag" data-id="${id}">
       ${name}
       <input type="hidden" name="assignees[]" value="${id}">
       &times;
     </span>`
  );
  // dọn
  $form.find('.employeeSearchPending').val('');
  $(this).parent().hide();
});

// 4) Xóa tag khi click
$(document).on('click', '.assignees-edit .employee-tag', function(){
  $(this).remove();
});
$(function(){
  // --- 1. Sau khi chọn suggestion → hide luôn input tìm ---
  $(document).on('click', '.employeeSuggestionsPending .suggestion', function(){
    const $cont = $(this).closest('.employee-search-container.pending');
    // ... (các dòng thêm tag hiện có của bạn) ...
    // Ẩn luôn hộp tìm kiếm
    $cont.hide();
  });

  // --- 2. Click ra ngoài + button để đóng hộp tìm kiếm ---
  $(document).on('click', function(e){
    // nếu click không nằm trong chính hộp tìm hoặc nút "+"
    if (!$(e.target).closest('.employee-search-container.pending, .btn-add-assignee').length) {
      $('.employee-search-container.pending').hide();
    }
  });
});


</script>


</body>
</html>
