<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Giao Việc & Quản Lý Tiến Độ</title>
    @vite([
        'resources/chamcong/giao_viec.css',
        'resources/chamcong/vendor.js',
    ])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
</head>
<body>
@include('chamcong.partials.admin_navbar')

<div class="container-body">
    @if(session('chamcong_flash_msg'))
        <div class='flash-msg'>{{ session('chamcong_flash_msg') }}</div>
    @endif

    <h1>Trang Giao Việc (Admin)</h1>
    <div class="tabs">
        <div class="tab-btn active" data-tab="tab-create">Giao Việc Mới</div>
        <div class="tab-btn" data-tab="tab-pending">Công việc đang giao</div>
        <div class="tab-btn" data-tab="tab-completed">Công việc đã hoàn thành</div>
    </div>

    <div id="tab-create" class="tab-content active">
        <h2>1) Giao Việc Mới</h2>
        <form method="POST" action="{{ route('chamcong.admin.tasks.create') }}">
            @csrf
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
            <button type="submit">Giao Việc</button>
        </form>
    </div>

    <div id="tab-pending" class="tab-content">
        <h2 class="section-title">2) Công việc đang giao (chưa hoàn thành)</h2>

        <div class="filter-section">
            <div class="pending-filter-wrapper">
                <label class="loc-nhan-vien" for="filterInputPending">Lọc nhân viên: </label>
                <div class="pending-input-container">
                    <input type="text" id="filterInputPending" placeholder="Nhập username..." autocomplete="off">
                    <div id="filterSuggestionsPending" class="filter-suggestions"></div>
                </div>
                <div id="selectedFiltersPending"></div>
            </div>

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
            @if(empty($pendingTasks))
                <p>Chưa có công việc nào đang giao.</p>
            @else
                @foreach($pendingTasks as $t)
                <div class="task-pending-box" id="task-{{ $t->id }}">
                    <form method="POST" action="{{ route('chamcong.admin.tasks.update') }}">
                        @csrf
                        <input type="hidden" name="task_id" value="{{ $t->id }}">

                        <div class="form-group">
                            <label><strong>Tên công việc:</strong></label>
                            <input type="text" name="task_name" class="form-control" value="{{ $t->task_name }}" required>
                        </div>

                        <div class="form-group">
                            <label><strong>Giao cho:</strong></label>
                            <div class="assignees-edit">
                                @foreach($assigneesByTaskId[$t->id] ?? [] as $uid)
                                    <span class="employee-tag" data-id="{{ $uid }}">
                                        {{ $allUsersMap[$uid] ?? $uid }}
                                        <input type="hidden" name="assignees[]" value="{{ $uid }}">
                                        &times;
                                    </span>
                                @endforeach
                                <button type="button" class="btn-add-assignee">+</button>
                                <div class="employee-search-container pending" style="display:none">
                                    <input type="text" class="employeeSearchPending" placeholder="Nhập username...">
                                    <div class="employeeSuggestionsPending"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><strong>Nội dung chính:</strong></label>
                            <textarea name="task_content" rows="3" cols="60" required>{{ $t->task_content }}</textarea>
                        </div>

                        <div class="form-group">
                            <label><strong>Hoàn thành trước:</strong></label>
                            <input type="date" name="due_date" value="{{ $t->due_date }}">
                        </div>

                        <div class="form-group">
                            <label><strong>Ghi chú tổng:</strong></label>
                            <textarea name="general_note" rows="2" cols="60">{{ $t->general_note }}</textarea>
                        </div>

                        <h4>Tiến độ chi tiết:</h4>
                        <table class="subtask-table subtask-table-pending" border="1" cellpadding="5" cellspacing="0">
                            <tr>
                                <th>Nội dung</th>
                                <th>Ghi chú</th>
                                <th>Hạn</th>
                                <th>Xóa</th>
                            </tr>
                            @foreach($subTasksByTask[$t->id] ?? [] as $sb)
                                <tr>
                                    <td>
                                        <input type="hidden" name="update_progress_id[]" value="{{ $sb->id }}">
                                        <textarea name="update_progress_content[]" rows="1">{{ $sb->progress_content }}</textarea>
                                    </td>
                                    <td><textarea name="update_progress_note[]" rows="1">{{ $sb->progress_note }}</textarea></td>
                                    <td><input type="date" name="update_progress_due[]" value="{{ $sb->due_date }}"></td>
                                    <td>
                                        <button type="button" class="btn-delete-progress" data-progress-id="{{ $sb->id }}">Xóa</button>
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td><textarea name="new_progress_content[]" rows="1" placeholder="Tiến độ mới"></textarea></td>
                                <td><textarea name="new_progress_note[]" rows="1" placeholder="Ghi chú"></textarea></td>
                                <td><input type="date" name="new_progress_due[]" placeholder="Hạn"></td>
                                <td></td>
                            </tr>
                        </table>

                        <div class="form-actions">
                            <button type="submit">Cập nhật</button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('chamcong.admin.tasks.delete') }}" onsubmit="return confirm('Xóa công việc này?');">
                        @csrf
                        <input type="hidden" name="task_id" value="{{ $t->id }}">
                        <button type="submit" class="btn-delete-task">Xóa công việc</button>
                    </form>
                </div>
                @endforeach
            @endif
        </div>

        <div class="pagination-controls" id="paginationPending"></div>
    </div>

    <div id="tab-completed" class="tab-content">
        <h2 class="section-title">3) Công việc đã hoàn thành</h2>

        <div class="filter-section">
            <div class="pending-filter-wrapper">
                <label class="loc-nhan-vien" for="filterInputCompleted">Lọc nhân viên: </label>
                <div class="pending-input-container">
                    <input type="text" id="filterInputCompleted" placeholder="Nhập username..." autocomplete="off">
                    <div id="filterSuggestionsCompleted" class="filter-suggestions"></div>
                </div>
                <div id="selectedFiltersCompleted"></div>
            </div>
            <div class="hien-thi-so-trang">
                <label class="hien-thi" for="tasksPerPageCompleted" style="margin-left:10px;">Hiển thị:</label>
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
            @if(empty($completedTasks))
                <p>Chưa có công việc hoàn thành.</p>
            @else
                @foreach($completedTasks as $t)
                    <div class="task-completed-box" id="task-{{ $t->id }}">
                        @php
                            $assigneesText = '';
                            if (!empty($assigneesByTaskId[$t->id])) {
                                $names = [];
                                foreach ($assigneesByTaskId[$t->id] as $uid) {
                                    if (!empty($allUsersMap[$uid])) {
                                        $names[] = $allUsersMap[$uid];
                                    }
                                }
                                $assigneesText = implode(', ', $names);
                            }
                        @endphp
                        <div class="assignees-text" style="display:none;">{{ $assigneesText }}</div>
                        <p><strong>Tên công việc:</strong> {{ $t->task_name }}</p>
                        <p><strong>Nội dung chính:</strong> {!! nl2br(e($t->task_content)) !!}</p>
                        <p><strong>Giao cho:</strong> {{ $assigneesText }}</p>
                        <p><strong>Hoàn thành trước:</strong> {{ $t->due_date ? date('d/m/Y', strtotime($t->due_date)) : '' }}</p>
                        <p><strong>Ghi chú tổng:</strong> {!! nl2br(e($t->general_note)) !!}</p>
                        <p><strong>Hoàn thành lúc:</strong> {{ $t->completed_at ? date('H:i - d/m/Y', strtotime($t->completed_at)) : '' }}</p>
                        <p><strong>Log:</strong> {{ $t->completion_log }}</p>
                        <h4>Tiến độ chi tiết:</h4>
                        <table class="subtask-table">
                            <tr>
                                <th>Nội dung</th>
                                <th>Ghi chú</th>
                                <th>Hạn</th>
                                <th>Trạng thái</th>
                            </tr>
                            @foreach($subTasksByTask[$t->id] ?? [] as $sb)
                                <tr>
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
                        </table>
                    </div>
                @endforeach
            @endif
        </div>
        <div class="pagination-controls" id="paginationCompleted"></div>
    </div>
</div>

<form id="deleteProgressForm" method="POST" action="{{ route('chamcong.admin.tasks.delete-progress') }}" style="display:none;">
    @csrf
    <input type="hidden" name="progress_id" id="deleteProgressId">
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.tab-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.tab-btn').forEach(function(b){ b.classList.remove('active'); });
      document.querySelectorAll('.tab-content').forEach(function(tc){ tc.classList.remove('active'); });
      btn.classList.add('active');
      var target = btn.getAttribute('data-tab');
      var el = document.getElementById(target);
      if (el) el.classList.add('active');
    });
  });
});
</script>

<script type="module">
var employees = @json($allUsers);

// Popup hoàn thành task (admin)
var newCompletedTasks = @json($newTasksForPopup);
function showNextPopup(index) {
  if (index >= newCompletedTasks.length) return;
  var task = newCompletedTasks[index];
  var popupHtml = '<div id="popupOverlay" style="position: fixed; top:0; left:0; right:0; bottom:0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center;">' +
    '<div id="popupBox" style="background: #fff; padding: 20px; border-radius: 5px; text-align: center;">' +
    '<p><strong>' + task.task_name + '</strong> của ' + (task.assignees || '') + ' đã hoàn thành ' + (task.completion_log || '') + '.</p>' +
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
  if (newCompletedTasks.length > 0) { showNextPopup(0); }
});

// Pagination
var currentPagePending = 1;
var currentPageCompleted = 1;
function paginateTasks(containerSelector, tasksPerPage, paginationContainerID) {
  var container = $(containerSelector);
  var tasks = container.children(".task-pending-box, .task-completed-box");
  tasks.each(function(index){ $(this).attr("data-index", index + 1); });
  if(tasksPerPage === "all") {
    tasks.show(); $("#" + paginationContainerID).empty(); return;
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
  if(totalPages > 1){
    if(currentPage > 1){
      pagDiv.append('<button onclick="changePage(\\''+containerSelector+'\\', '+(currentPage-1)+', \\''+paginationContainerID+'\\')">Trước</button>');
    }
    pagDiv.append(' Page ' + currentPage + ' of ' + totalPages + ' ');
    if(currentPage < totalPages){
      pagDiv.append('<button onclick="changePage(\\''+containerSelector+'\\', '+(currentPage+1)+', \\''+paginationContainerID+'\\')">Sau</button>');
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

// Filter pending
var selectedFiltersPending = [];
function renderFilterSuggestionsPending(query) {
  const suggestionsDiv = $("#filterSuggestionsPending");
  suggestionsDiv.empty();
  if (!query.trim()) { suggestionsDiv.hide(); return; }
  const filtered = employees.filter(emp => emp.username.toLowerCase().includes(query.toLowerCase()) && !selectedFiltersPending.includes(emp.username));
  if (!filtered.length) { suggestionsDiv.hide(); return; }
  filtered.forEach(emp => {
    suggestionsDiv.append(`<div class="suggestion-item" data-username="${emp.username}" style="padding:5px;cursor:pointer;">${emp.username}</div>`);
  });
  suggestionsDiv.show();
}
function filterTasksPending() {
  $("#tasksContainerPending .task-pending-box").each(function() {
    const assignees = $(this).find(".assignees-edit .employee-tag").map(function() {
      return $(this).text().replace("×", "").trim().toLowerCase();
    }).get();
    const ok = selectedFiltersPending.every(f => assignees.includes(f.toLowerCase()));
    $(this).toggle(ok);
  });
}
$(function() {
  $("#filterInputPending").on("input", function() { renderFilterSuggestionsPending($(this).val()); });
  $(document).on("click", "#filterSuggestionsPending .suggestion-item", function() {
    const uname = $(this).data("username");
    selectedFiltersPending.push(uname);
    $("#selectedFiltersPending").append(`<div class="filter-tag" data-username="${uname}" style="margin:2px;padding:2px 5px;border:1px solid #aaa;background:#eef;display:inline-block;cursor:pointer;">${uname} &times;</div>`);
    $("#filterInputPending").val(""); $("#filterSuggestionsPending").hide(); filterTasksPending();
  });
  $(document).on("click", "#selectedFiltersPending .filter-tag", function() {
    const uname = $(this).data("username");
    selectedFiltersPending = selectedFiltersPending.filter(u => u !== uname);
    $(this).remove(); filterTasksPending();
  });
  $(document).click(function(e) {
    if (!$(e.target).closest("#filterInputPending, #filterSuggestionsPending").length) {
      $("#filterSuggestionsPending").hide();
    }
  });
});

// Filter completed
var selectedFiltersCompleted = [];
function renderFilterSuggestionsCompleted(query) {
  var suggestionsDiv = $("#filterSuggestionsCompleted");
  suggestionsDiv.empty();
  if (query.trim() === "") { suggestionsDiv.hide(); return; }
  var filtered = employees.filter(function(emp){ return emp.username.toLowerCase().indexOf(query.toLowerCase()) !== -1; });
  if (filtered.length === 0) { suggestionsDiv.hide(); return; }
  filtered.forEach(function(emp){
    if (selectedFiltersCompleted.indexOf(emp.username) !== -1) return;
    suggestionsDiv.append("<div class='suggestion-item' data-username='"+emp.username+"' style='padding:5px;cursor:pointer;'>"+emp.username+"</div>");
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
  $("#filterInputCompleted").on("keyup", function(){ renderFilterSuggestionsCompleted($(this).val()); });
  $(document).on("click", "#filterSuggestionsCompleted .suggestion-item", function(){
    var uname = $(this).data("username");
    if (selectedFiltersCompleted.indexOf(uname) === -1) {
      selectedFiltersCompleted.push(uname);
      $("#selectedFiltersCompleted").append("<div class='filter-tag' data-username='"+uname+"' style='display:inline-block;margin:2px;padding:2px 5px;border:1px solid #aaa;background:#eef;cursor:pointer;'>"+uname+" &times;</div>");
      filterTasksCompleted();
    }
    $("#filterInputCompleted").val("");
    $("#filterSuggestionsCompleted").hide();
  });
  $(document).on("click", "#selectedFiltersCompleted .filter-tag", function(){
    var uname = $(this).data("username");
    selectedFiltersCompleted = selectedFiltersCompleted.filter(function(x){ return x !== uname; });
    $(this).remove();
    filterTasksCompleted();
  });
  $(document).click(function(e){
    if(!$(e.target).closest("#filterInputCompleted, #filterSuggestionsCompleted").length){
      $("#filterSuggestionsCompleted").hide();
    }
  });
});

// Search/assign employees
var selectedEmployees = {};
function renderSuggestions(query) {
  var suggestionsDiv = $("#employeeSuggestions");
  suggestionsDiv.empty();
  if (query.trim() === "") { suggestionsDiv.hide(); return; }
  var filtered = employees.filter(function(emp) {
    return emp.username.toLowerCase().indexOf(query.toLowerCase()) !== -1;
  });
  if (filtered.length === 0) { suggestionsDiv.hide(); return; }
  filtered.forEach(function(emp) {
    if (selectedEmployees[emp.id]) return;
    suggestionsDiv.append("<div class='suggestion-item' data-id='" + emp.id + "' style='padding:5px; cursor:pointer;'>" + emp.username + "</div>");
  });
  suggestionsDiv.show();
}
$(document).ready(function() {
  $("#employeeSearch").on("keyup", function() { renderSuggestions($(this).val()); });
  $(document).on("click", ".suggestion-item", function() {
    var empId = $(this).data("id");
    var empName = $(this).text();
    if (!selectedEmployees[empId]) {
      selectedEmployees[empId] = empName;
      $("#selectedEmployees").append("<div class='employee-tag' data-id='" + empId + "' style='display:inline-block; margin:2px; padding:2px 5px; border:1px solid #aaa; background:#eef; cursor:pointer;'>" + empName + " &times;<input type='hidden' name='assignees[]' value='" + empId + "'></div>");
    }
    $("#employeeSearch").val(""); $("#employeeSuggestions").hide();
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

// Assignees edit in pending
$(document).on('click', '.btn-add-assignee', function(){
  var $cont = $(this).siblings('.employee-search-container.pending');
  $cont.toggle();
  $cont.find('input').focus();
});
$(document).on('input', '.employeeSearchPending', function(){
  var $list = $(this).siblings('.employeeSuggestionsPending');
  var q = $(this).val().toLowerCase();
  $list.empty();
  if (!q) return $list.hide();
  employees.forEach(function(emp){
    if (emp.username.toLowerCase().includes(q)) {
      if ($(this).closest('form').find('input[value="'+emp.id+'"]').length) return;
      $list.append(`<div class="suggestion" data-id="${emp.id}">${emp.username}</div>`);
    }
  }.bind(this));
  $list.show();
});
$(document).on('click', '.employeeSuggestionsPending .suggestion', function(){
  var id = $(this).data('id'),
      name = $(this).text(),
      $form = $(this).closest('form');
  $form.find('.assignees-edit').prepend(
    `<span class="employee-tag" data-id="${id}">
       ${name}
       <input type="hidden" name="assignees[]" value="${id}">
       &times;
     </span>`
  );
  $form.find('.employeeSearchPending').val('');
  $(this).parent().hide();
});
$(document).on('click', '.assignees-edit .employee-tag', function(){
  $(this).remove();
});

$(document).on('click', '.btn-delete-progress', function(){
  const pid = $(this).data('progress-id');
  if (!pid) return;
  if (!confirm('Xóa tiến độ này?')) return;
  document.getElementById('deleteProgressId').value = pid;
  document.getElementById('deleteProgressForm').submit();
});

// Auto add new progress row
function appendProgressRow() {
  const idx = $("#progressContainer .progress-grid").length + 1;
  $("#progressContainer").append(
    $("<div>", { class:"progress-grid" }).html(
      `<textarea name="progress_content[]" rows="1" placeholder="Tiến độ ${idx}"></textarea>
       <textarea name="progress_note[]" rows="1" placeholder="Ghi chú tiến độ ${idx}"></textarea>
       <input type="date" name="progress_due_date[]" placeholder="Hạn tiến độ ${idx}">`
    )
  );
}
$(document).on("input", "#progressContainer .progress-grid:last-child textarea[name='progress_content[]']", function () {
  if ($(this).val().trim() !== "") appendProgressRow();
});

// expose functions used by onclick in generated HTML
window.paginateTasks = paginateTasks;
window.changePage = changePage;
window.closePopup = closePopup;
</script>
</body>
</html>
