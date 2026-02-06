<?php
// user_tasks.php (phiên bản cập nhật)
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require 'db.php';

// Kiểm tra đăng nhập của user
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

function format_dmy($date) {
    return $date
      ? date('d/m/Y', strtotime($date))
      : '';
}
function format_status_time($datetime) {
    if (!$datetime) return '';
    return date('H:i - d/m/Y', strtotime($datetime));
}


$user_id = $_SESSION['user_id'];
/* ============================================
   ĐÁNH DẤU ĐÃ XEM CÁC TASK ĐƯỢC GIAO
   ============================================ */
   $conn->prepare("
   UPDATE task_assignees
   SET seen = 1
   WHERE user_id = :uid AND seen = 0
")->execute(['uid' => $user_id]);
$popupMsg = '';
if (isset($_SESSION['popup_msg'])) {
    $popupMsg = $_SESSION['popup_msg'];
    unset($_SESSION['popup_msg']);  // Xóa ngay để chỉ hiển thị 1 lần
}

// Xử lý khi user bấm nút "Hoàn thành" cho 1 sub-task
if (isset($_POST['complete_subtask'])) {
    $progressID = (int)$_POST['progress_id'];
    
    // Cập nhật sub-task: đánh dấu hoàn thành và lưu thời gian hoàn thành
    $stmtUp = $conn->prepare("UPDATE task_progress SET is_completed = 1, completed_at = NOW() WHERE id = :pid");
    $stmtUp->execute(['pid' => $progressID]);
    
    // Lấy task_id của sub-task vừa cập nhật
    $stmtTaskId = $conn->prepare("SELECT task_id FROM task_progress WHERE id = :pid LIMIT 1");
    $stmtTaskId->execute(['pid' => $progressID]);
    $record = $stmtTaskId->fetch(PDO::FETCH_ASSOC);
    
    if ($record) {
        $taskID = $record['task_id'];
        // Kiểm tra xem còn tiến độ nào chưa hoàn thành cho task đó không
        $stmtCheckAll = $conn->prepare("SELECT COUNT(*) AS pending FROM task_progress WHERE task_id = :tid AND is_completed = 0");
        $stmtCheckAll->execute(['tid' => $taskID]);
        $row = $stmtCheckAll->fetch(PDO::FETCH_ASSOC);
        
        if ($row && $row['pending'] == 0) {
            // Nếu tất cả sub-task đã hoàn thành, lấy hạn công việc của task
            $stmtTask = $conn->prepare("SELECT due_date FROM tasks WHERE id = :tid LIMIT 1");
            $stmtTask->execute(['tid' => $taskID]);
            $taskRow = $stmtTask->fetch(PDO::FETCH_ASSOC);
            $dueDate = $taskRow['due_date'];
            $nowTime = date("Y-m-d H:i:s");
            
            // So sánh thời gian hoàn thành với hạn (dùng strtotime)
            $dueTimestamp = strtotime($dueDate);
            $completeTimestamp = strtotime($nowTime);
            if ($completeTimestamp <= $dueTimestamp) {
                $completionLog = "Công việc hoàn thành đúng hạn";
                $popupMsg = "Chúc mừng, bạn đã hoàn thành xong công việc";
            } else {
                $completionLog = "Công việc hoàn thành chậm hơn dự kiến";
                $popupMsg = "Bạn đã hoàn thành công việc chậm hơn dự định";
            }
            // Cập nhật task: đánh dấu hoàn thành và lưu log kết quả
            $stmtUpdateTask = $conn->prepare("UPDATE tasks SET completed_at = :cat, completion_log = :clog WHERE id = :tid");
            $stmtUpdateTask->execute([
                'cat'  => $nowTime,
                'clog' => $completionLog,
                'tid'  => $taskID
            ]);
            // Sau khi hoàn thành, chuyển hướng kèm thông số để hiển thị popup
            $_SESSION['popup_msg'] = $popupMsg;
            header("Location: user_tasks.php#task-{$taskID}");
            exit;
        }
    }
}

// Lấy danh sách task được giao cho user
$stmt = $conn->prepare("
    SELECT t.* 
    FROM task_assignees ta
    JOIN tasks t ON ta.task_id = t.id
    WHERE ta.user_id = :uid
    ORDER BY t.id DESC
");
$stmt->execute(['uid' => $user_id]);
$myTasksAll = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Phân tách task theo trạng thái: đang thực hiện (pending) và đã hoàn thành
$pendingTasks = [];
$completedTasks = [];
foreach ($myTasksAll as $task) {
    if (empty($task['completed_at'])) {
        $pendingTasks[] = $task;
    } else {
        $completedTasks[] = $task;
    }
}

// Lấy tất cả sub-task của các task vừa lấy
$taskIDs = array_column($myTasksAll, 'id');
$subTaskByTask = [];
if (!empty($taskIDs)) {
    $inList = implode(',', $taskIDs);
    $sqlSub = "SELECT * FROM task_progress WHERE task_id IN ($inList) ORDER BY id ASC";
    $rowsSub = $conn->query($sqlSub)->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rowsSub as $sub) {
        $subTaskByTask[$sub['task_id']][] = $sub;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Công Việc Của Bạn</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet"
          href="user_tasks.css?v=<?= filemtime(__DIR__ . '/user_tasks.css') ?>">


</head>
<body>


<div class="container-body">
<a href="dashboard.php" class="logo-link">
    <img src="logo.png" alt="Logo Công Ty" class="company-logo">
</a>
  <h1>Danh sách công việc của <?php echo htmlspecialchars($_SESSION['username']); ?></h1>

  <?php if (!empty($popupMsg)): ?>
      <div id="popupOverlay" class="popup-overlay">
          <div class="popup-box">
              <p><?php echo htmlspecialchars($popupMsg); ?></p>
              <button class="close-popup" onclick="closePopup()">Đóng</button>
          </div>
      </div>
  <?php endif; ?>

  <!-- Các nút chuyển tab -->
  <div class="tabs">
      <div class="tab-btn active" data-tab="tab-pending">Công việc đang thực hiện</div>
      <div class="tab-btn" data-tab="tab-completed">Công việc đã hoàn thành</div>
  </div>

  <!-- Tab 1: Công việc đang thực hiện -->
  <div id="tab-pending" class="tab-content active">
      <h2>Công việc đang thực hiện</h2>
      <!-- Phần lọc/Phân trang cho pending tasks -->
      <div class="filter-section">
          <label for="tasksPerPagePending">Hiển thị:</label>
          <select id="tasksPerPagePending">
              <option value="5">5</option>
              <option value="10" selected>10</option>
              <option value="30">30</option>
              <option value="50">50</option>
              <option value="all">Toàn bộ</option>
          </select>
      </div>
      <div class="tasks-container" id="tasksContainerPending">
          <?php if (count($pendingTasks) == 0): ?>
              <p>Bạn chưa có công việc nào đang thực hiện.</p>
          <?php else: ?>
              <?php foreach ($pendingTasks as $t): ?>
                  <div class="task-pending-box" id="task-<?php echo $t['id']; ?>">
                      <h3>Công việc: "<?php echo htmlspecialchars($t['task_name']); ?>"</h3>
                      <p><strong>Nội dung:</strong> <?php echo nl2br(htmlspecialchars($t['task_content'])); ?></p>
                      <p><strong>Hoàn thành trước:</strong> <?php echo format_dmy($t['due_date']); ?></p>
                      <p><strong>Ghi chú tổng:</strong> <?php echo nl2br(htmlspecialchars($t['general_note'])); ?></p>
                      <h4>Tiến độ chi tiết:</h4>
                      <div class="subtask-table-container">
                        <table class="subtask-table">
                            <tr>
                                <th>STT</th>
                                <th>Nội dung</th>
                                <th>Ghi chú</th>
                                <th>Hạn</th>
                                <th>Trạng thái</th>
                            </tr>
                            <?php 
                            $i = 1; 
                            if (!empty($subTaskByTask[$t['id']])):
                                foreach ($subTaskByTask[$t['id']] as $sb):
                            ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo nl2br(htmlspecialchars($sb['progress_content'])); ?></td>
                                <td><?php echo htmlspecialchars($sb['progress_note']); ?></td>
                                <td><?php echo $sb['due_date']; ?></td>
                                <td>
                                    <?php if ($sb['is_completed']): ?>
                                        Đã xong lúc <?php echo format_status_time($sb['completed_at']); ?>
                                        <?php else: ?>
                                        <form method="POST"
                                                action="user_tasks.php#task-<?php echo $t['id']; ?>"
                                                style="display:inline;"
                                                onsubmit="return confirm('Xác nhận hoàn thành tiến độ này?');">
                                            <input type="hidden" name="progress_id" value="<?php echo $sb['id']; ?>">
                                            <button name="complete_subtask">Xác nhận HOÀN THÀNH</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php 
                                endforeach;
                            else:
                            ?>
                            <tr><td colspan="5"><i>Không có tiến độ</i></td></tr>
                            <?php endif; ?>
                          </table>
                      </div>

                  </div>
              <?php endforeach; ?>
          <?php endif; ?>
      </div>
      <!-- Nút phân trang hiển thị cho pending tasks -->
      <div class="pagination-controls" id="paginationPending"></div>
  </div>

  <!-- Tab 2: Công việc đã hoàn thành -->
  <div id="tab-completed" class="tab-content">
      <h2>Công việc đã hoàn thành</h2>
      <!-- Phần lọc/Phân trang cho completed tasks -->
      <div class="filter-section">
          <label for="tasksPerPageCompleted">Hiển thị:</label>
          <select id="tasksPerPageCompleted">
              <option value="5">5</option>
              <option value="10" selected>10</option>
              <option value="30">30</option>
              <option value="50">50</option>
              <option value="all">Toàn bộ</option>
          </select>
      </div>
      <div class="tasks-container" id="tasksContainerCompleted">
          <?php if (count($completedTasks) == 0): ?>
              <p>Chưa có công việc hoàn thành.</p>
          <?php else: ?>
              <?php foreach ($completedTasks as $t): ?>
                  <div class="task-completed-box" id="task-<?php echo $t['id']; ?>">
                      <h3>Công việc: "<?php echo htmlspecialchars($t['task_name']); ?>"</h3>
                      <p><strong>Nội dung:</strong> <?php echo nl2br(htmlspecialchars($t['task_content'])); ?></p>
                      <p><strong>Hoàn thành trước:</strong> <?php echo format_dmy($t['due_date']); ?></p>
                      <p><strong>Ghi chú tổng:</strong> <?php echo nl2br(htmlspecialchars($t['general_note'])); ?></p>
                      <p><strong>Hoàn thành lúc:</strong> <?php echo format_status_time($t['completed_at']); ?></p>
                      <p><strong>Log:</strong> <?php echo $t['completion_log']; ?></p>
                      <h4>Tiến độ chi tiết:</h4>
                      <div class="subtask-table-container">
                        <table class="subtask-table">
                            <tr>
                                <th>STT</th>
                                <th>Nội dung</th>
                                <th>Ghi chú</th>
                                <th>Hạn</th>
                                <th>Trạng thái</th>
                            </tr>
                            <?php 
                            $i = 1;
                            if (!empty($subTaskByTask[$t['id']])):
                                foreach ($subTaskByTask[$t['id']] as $sb):
                            ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo nl2br(htmlspecialchars($sb['progress_content'])); ?></td>
                                <td><?php echo htmlspecialchars($sb['progress_note']); ?></td>
                                <td><?php echo $sb['due_date']; ?></td>
                                <td>
                                    <?php if ($sb['is_completed']): ?>
                                        Đã xong lúc <?php echo format_status_time($sb['completed_at']); ?>
                                    <?php else: ?>
                                        Chưa hoàn thành
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php 
                                endforeach;
                            else:
                            ?>
                            <tr><td colspan="5"><i>Không có tiến độ</i></td></tr>
                            <?php endif; ?>
                        </table>
                      </div>

                  </div>
              <?php endforeach; ?>
          <?php endif; ?>
      </div>
      <!-- Nút phân trang hiển thị cho completed tasks -->
      <div class="pagination-controls" id="paginationCompleted"></div>
  </div>

</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Chuyển đổi tab
document.querySelectorAll('.tab-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById(btn.dataset.tab).classList.add('active');
        // Khi chuyển tab, gọi phân trang tương ứng
        if (btn.dataset.tab === "tab-pending") {
            paginateTasks("#tasksContainerPending", $("#tasksPerPagePending").val(), "paginationPending");
        } else if (btn.dataset.tab === "tab-completed") {
            paginateTasks("#tasksContainerCompleted", $("#tasksPerPageCompleted").val(), "paginationCompleted");
        }
    });
});

// Hàm đóng popup
function closePopup() {
    document.getElementById('popupOverlay').style.display = 'none';
}

// Các biến phân trang cho pending và completed
var currentPagePending = 1;
var currentPageCompleted = 1;

// Hàm phân trang chung
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
    
    // Thiết lập điều khiển phân trang
    var totalTasks = tasks.length;
    var totalPages = Math.ceil(totalTasks/tasksPerPage);
    var pagDiv = $("#" + paginationContainerID);
    pagDiv.empty();
    if(totalPages > 1) {
        if(currentPage > 1) {
            pagDiv.append('<button onclick="changePage(\''+containerSelector+'\', '+(currentPage-1)+', \''+paginationContainerID+'\')">Trước</button>');
        }
        pagDiv.append(' Page ' + currentPage + ' of ' + totalPages + ' ');
        if(currentPage < totalPages) {
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

// Gọi phân trang khi dropdown thay đổi
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

// Gọi phân trang ban đầu khi trang được load
$(document).ready(function(){
    paginateTasks("#tasksContainerPending", $("#tasksPerPagePending").val(), "paginationPending");
    paginateTasks("#tasksContainerCompleted", $("#tasksPerPageCompleted").val(), "paginationCompleted");
});
/* Gắn class expandable + nút show‑more */
function makeExpandable(scope){
  $(scope).find('.task-pending-box, .task-completed-box').each(function(){
      $(this).find('p').each(function(){
          const label = $(this).find('strong').text().trim().toLowerCase();
          if(label.startsWith('nội dung') || label.startsWith('ghi chú tổng')){
              if($(this).text().trim().length > 140){     // chỉ gắn nếu đủ dài
                  $(this).addClass('expandable collapsed');
                  $('<span class="show-more">Xem thêm</span>').insertAfter(this);
              }
          }
      });
  });
}

/* toggle – chỉ đăng ký 1 lần */
$(document).on('click', '.show-more', function(){
  const $p = $(this).prev('.expandable');
  $p.toggleClass('collapsed');
  $(this).text($p.hasClass('collapsed') ? 'Xem thêm' : 'Thu gọn');
});

/* chạy khi trang load và sau khi phân trang */
$(document).ready(function(){
  makeExpandable('#tab-pending');
  makeExpandable('#tab-completed');
});

</script>

</body>
</html>
