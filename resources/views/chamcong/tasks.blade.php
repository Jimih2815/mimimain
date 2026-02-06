<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Công Việc Của Bạn</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/chamcong/user_tasks.css', 'resources/chamcong/vendor.js'])
</head>
<body>
<div class="container-body">
<a href="{{ route('chamcong.dashboard') }}" class="logo-link">
    <img src="{{ Vite::asset('resources/chamcong/logo.png') }}" alt="Logo Công Ty" class="company-logo">
</a>
  <h1>Danh sách công việc của {{ session('chamcong_username') }}</h1>

  @if(!empty($popupMsg))
      <div id="popupOverlay" class="popup-overlay">
          <div class="popup-box">
              <p>{{ $popupMsg }}</p>
              <button class="close-popup" onclick="closePopup()">Đóng</button>
          </div>
      </div>
  @endif

  <div class="tabs">
      <div class="tab-btn active" data-tab="tab-pending">Công việc đang thực hiện</div>
      <div class="tab-btn" data-tab="tab-completed">Công việc đã hoàn thành</div>
  </div>

  <div id="tab-pending" class="tab-content active">
      <h2>Công việc đang thực hiện</h2>
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
          @if(count($pendingTasks) == 0)
              <p>Bạn chưa có công việc nào đang thực hiện.</p>
          @else
              @foreach($pendingTasks as $t)
                  <div class="task-pending-box" id="task-{{ $t->id }}">
                      <h3>Công việc: "{{ $t->task_name }}"</h3>
                      <p><strong>Nội dung:</strong> {!! nl2br(e($t->task_content)) !!}</p>
                      <p><strong>Hoàn thành trước:</strong> {{ $t->due_date ? date('d/m/Y', strtotime($t->due_date)) : '' }}</p>
                      <p><strong>Ghi chú tổng:</strong> {!! nl2br(e($t->general_note)) !!}</p>
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
                            @php $i = 1; @endphp
                            @if(!empty($subTaskByTask[$t->id]))
                                @foreach($subTaskByTask[$t->id] as $sb)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{!! nl2br(e($sb->progress_content)) !!}</td>
                                        <td>{{ $sb->progress_note }}</td>
                                        <td>{{ $sb->due_date }}</td>
                                        <td>
                                            @if($sb->is_completed)
                                                Đã xong lúc {{ $sb->completed_at ? date('H:i - d/m/Y', strtotime($sb->completed_at)) : '' }}
                                            @else
                                                <form method="POST" action="{{ route('chamcong.tasks.complete') }}#task-{{ $t->id }}" style="display:inline;" onsubmit="return confirm('Xác nhận hoàn thành tiến độ này?');">
                                                    @csrf
                                                    <input type="hidden" name="progress_id" value="{{ $sb->id }}">
                                                    <button name="complete_subtask">Xác nhận HOÀN THÀNH</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr><td colspan="5"><i>Không có tiến độ</i></td></tr>
                            @endif
                          </table>
                      </div>

                  </div>
              @endforeach
          @endif
      </div>
      <div class="pagination-controls" id="paginationPending"></div>
  </div>

  <div id="tab-completed" class="tab-content">
      <h2>Công việc đã hoàn thành</h2>
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
          @if(count($completedTasks) == 0)
              <p>Chưa có công việc hoàn thành.</p>
          @else
              @foreach($completedTasks as $t)
                  <div class="task-completed-box" id="task-{{ $t->id }}">
                      <h3>Công việc: "{{ $t->task_name }}"</h3>
                      <p><strong>Nội dung:</strong> {!! nl2br(e($t->task_content)) !!}</p>
                      <p><strong>Hoàn thành trước:</strong> {{ $t->due_date ? date('d/m/Y', strtotime($t->due_date)) : '' }}</p>
                      <p><strong>Ghi chú tổng:</strong> {!! nl2br(e($t->general_note)) !!}</p>
                      <p><strong>Hoàn thành lúc:</strong> {{ $t->completed_at ? date('H:i - d/m/Y', strtotime($t->completed_at)) : '' }}</p>
                      <p><strong>Log:</strong> {{ $t->completion_log }}</p>
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
                            @php $i = 1; @endphp
                            @if(!empty($subTaskByTask[$t->id]))
                                @foreach($subTaskByTask[$t->id] as $sb)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{!! nl2br(e($sb->progress_content)) !!}</td>
                                        <td>{{ $sb->progress_note }}</td>
                                        <td>{{ $sb->due_date }}</td>
                                        <td>
                                            @if($sb->is_completed)
                                                Đã xong lúc {{ $sb->completed_at ? date('H:i - d/m/Y', strtotime($sb->completed_at)) : '' }}
                                            @else
                                                Chưa hoàn thành
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr><td colspan="5"><i>Không có tiến độ</i></td></tr>
                            @endif
                        </table>
                      </div>
                  </div>
              @endforeach
          @endif
      </div>
      <div class="pagination-controls" id="paginationCompleted"></div>
  </div>

</div>
<script>
document.querySelectorAll('.tab-btn').forEach(function(btn) {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById(btn.dataset.tab).classList.add('active');
    if (btn.dataset.tab === "tab-pending") {
      paginateTasks("#tasksContainerPending", window.jQuery ? window.jQuery("#tasksPerPagePending").val() : "10", "paginationPending");
    } else if (btn.dataset.tab === "tab-completed") {
      paginateTasks("#tasksContainerCompleted", window.jQuery ? window.jQuery("#tasksPerPageCompleted").val() : "10", "paginationCompleted");
    }
  });
});

function closePopup() {
  document.getElementById('popupOverlay').style.display = 'none';
}

var currentPagePending = 1;
var currentPageCompleted = 1;

function paginateTasks(containerSelector, tasksPerPage, paginationContainerID) {
  var $ = window.jQuery;
  if (!$) return;
  var container = $(containerSelector);
  var tasks = container.children(".task-pending-box, .task-completed-box");
  tasks.each(function(index) { $(this).attr("data-index", index + 1); });
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

  var totalTasks = tasks.length;
  var totalPages = Math.ceil(totalTasks/tasksPerPage);
  var pagDiv = $("#" + paginationContainerID);
  pagDiv.empty();
  if(totalPages > 1) {
    if(currentPage > 1) {
      pagDiv.append('<button onclick="changePage(\'' + containerSelector + '\', ' + (currentPage-1) + ', \'' + paginationContainerID + '\')">Trước</button>');
    }
    pagDiv.append(' Page ' + currentPage + ' of ' + totalPages + ' ');
    if(currentPage < totalPages) {
      pagDiv.append('<button onclick="changePage(\'' + containerSelector + '\', ' + (currentPage+1) + ', \'' + paginationContainerID + '\')">Sau</button>');
    }
  }
}

function changePage(containerSelector, newPage, paginationContainerID){
  var $ = window.jQuery;
  if (!$) return;
  if(containerSelector === "#tasksContainerPending"){
    currentPagePending = newPage;
    paginateTasks(containerSelector, $("#tasksPerPagePending").val(), paginationContainerID);
  } else {
    currentPageCompleted = newPage;
    paginateTasks(containerSelector, $("#tasksPerPageCompleted").val(), paginationContainerID);
  }
  window.scrollTo(0, 0);
}

function makeExpandable(scope){
  var $ = window.jQuery;
  if (!$) return;
  $(scope).find('.task-pending-box, .task-completed-box').each(function(){
    $(this).find('p').each(function(){
      const label = $(this).find('strong').text().trim().toLowerCase();
      if(label.startsWith('nội dung') || label.startsWith('ghi chú tổng')){
        if($(this).text().trim().length > 140){
          $(this).addClass('expandable collapsed');
          $('<span class="show-more">Xem thêm</span>').insertAfter(this);
        }
      }
    });
  });
}

window.addEventListener('load', function(){
  var $ = window.jQuery;
  if (!$) return;

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

  paginateTasks("#tasksContainerPending", $("#tasksPerPagePending").val(), "paginationPending");
  paginateTasks("#tasksContainerCompleted", $("#tasksPerPageCompleted").val(), "paginationCompleted");

  $(document).on('click', '.show-more', function(){
    const $p = $(this).prev('.expandable');
    $p.toggleClass('collapsed');
    $(this).text($p.hasClass('collapsed') ? 'Xem thêm' : 'Thu gọn');
  });
  makeExpandable('#tab-pending');
  makeExpandable('#tab-completed');
});
</script>
</body>
</html>