<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>MiMi Quản Lý Chấm Công</title>
    @vite([
        'resources/chamcong/admin.css',
        'resources/chamcong/vendor.js',
    ])
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        .disabled { background-color: #eee; }
    </style>
</head>
<body>
@include('chamcong.partials.admin_navbar')
@if(session('chamcong_flash_msg'))
    <div id="flashOverlay" class="flash-overlay">
        <div id="flashPopup" class="flash-popup">
            {!! session('chamcong_flash_msg') !!}
        </div>
    </div>
@endif

<h1>Quản lý Nhân viên</h1>
<div class="container">
    <div class="tabs" data-initial-tab="{{ $activeTab ?? 'modul1' }}">
      <button class="tab-btn {{ ($activeTab ?? 'modul1') === 'modul1' ? 'active' : '' }}" data-target="modul1" type="button">Thêm nhân viên</button>
      <button class="tab-btn {{ ($activeTab ?? 'modul1') === 'modul2' ? 'active' : '' }}" data-target="modul2" type="button">Danh sách nhân viên</button>
      <button class="tab-btn {{ ($activeTab ?? 'modul1') === 'modul3' ? 'active' : '' }}" data-target="modul3" type="button">Tính lương</button>
      <button class="tab-btn {{ ($activeTab ?? 'modul1') === 'modul4' ? 'active' : '' }}" data-target="modul4" type="button">Quản lý chấm công</button>
      <button class="tab-btn {{ ($activeTab ?? 'modul1') === 'modul5' ? 'active' : '' }}" data-target="modul5" type="button">Chấm công bù</button>
    </div>

    <div id="modul1" class="tab-content {{ ($activeTab ?? 'modul1') === 'modul1' ? 'active' : '' }}">
        <h2>Thêm nhân viên</h2>
        <form method="POST" action="{{ route('chamcong.admin.users.add') }}" class="form-add-employee">
            @csrf
            <table>
                <tr>
                    <td><label>Username:</label></td>
                    <td><input type="text" name="username" required></td>
                    <td><label>Password:</label></td>
                    <td><input type="text" name="password" required></td>
                </tr>
                <tr>
                    <td><label>Loại nhân viên:</label></td>
                    <td>
                        <select name="employee_type">
                            <option value="chinh_thuc">Chính thức</option>
                            <option value="thoi_vu">Thời vụ</option>
                        </select>
                    </td>
                    <td><label>Lương tháng:</label></td>
                    <td>
                        <input type="number" name="base_salary" class="base_new">
                    </td>
                </tr>
                <tr>
                    <td><label>Tổng giờ yêu cầu làm:</label></td>
                    <td><input type="number" name="required_hours" class="req_new"></td>
                    <td><label>Lương/giờ:</label></td>
                    <td><input type="number" name="hourly_rate" class="hr_new"></td>
                </tr>
            </table>
            <button type="submit">Thêm user</button>
        </form>
    </div>

    <div id="modul2" class="tab-content {{ ($activeTab ?? 'modul1') === 'modul2' ? 'active' : '' }}">
        <h2>Danh sách nhân viên</h2>
        <table border="1" cellpadding="5" cellspacing="0">
            <tr>
                <th>Trạng thái</th>
                <th>Username</th>
                <th>Bỏ qua GPS?</th>
                <th>Loại NV</th>
                <th>Lương tháng (NV chính thức)</th>
                <th>Tổng giờ yêu cầu</th>
                <th>Lương/giờ (NV thời vụ)</th>
                <th colspan="2">Hành động</th>
            </tr>

            @foreach($users as $u)
                @php
                    $isWorking = !empty($workingMap[$u->id]);
                    $color = $isWorking ? '#139b13' : '#b83232';
                    $confirmMsg = $isWorking
                        ? "Bạn có chắc chắn muốn Check-out hộ nhân viên {$u->username} không?"
                        : "Bạn có chắc chắn muốn Check-in hộ nhân viên {$u->username} không?";
                @endphp
                <tr>
                    <td>
                        <form method="POST" action="{{ route('chamcong.admin.toggle-status') }}" onsubmit="return confirm('{{ $confirmMsg }}');" style="display:inline;">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $u->id }}">
                            <button type="submit" style="border:none; background:none; cursor:pointer;">
                                <div style="width:20px; height:20px; border-radius:50%; background:{{ $color }}; margin:auto;"></div>
                            </button>
                        </form>
                    </td>

                    <form method="POST" action="{{ route('chamcong.admin.users.update') }}">
                        @csrf
                        <td>{{ $u->username }}</td>
                        <td>
                            <input type="checkbox" name="ignore_location" value="1" {{ $u->ignore_location ? 'checked' : '' }}>
                        </td>
                        <td>
                            <select name="employee_type">
                                <option value="chinh_thuc" {{ $u->employee_type === 'chinh_thuc' ? 'selected' : '' }}>Chính thức</option>
                                <option value="thoi_vu" {{ $u->employee_type === 'thoi_vu' ? 'selected' : '' }}>Thời vụ</option>
                            </select>
                        </td>
                        <td><input type="number" name="base_salary" value="{{ $u->base_salary }}" class="base_salary"></td>
                        <td><input type="number" name="required_hours" value="{{ $u->required_hours }}" class="required_hours"></td>
                        <td><input type="number" name="hourly_rate" value="{{ $u->hourly_rate }}" class="hourly_rate"></td>
                        <td>
                            <input type="hidden" name="user_id" value="{{ $u->id }}">
                            <button type="submit">Lưu</button>
                        </td>
                    </form>

                    <td>
                        <form method="POST" action="{{ route('chamcong.admin.users.delete') }}" onsubmit="return confirm('Bạn chắc chắn muốn xóa user {{ $u->username }}?');">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $u->id }}">
                            <button type="submit" class="btn-delete-user">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>

    <div id="modul3" class="tab-content {{ ($activeTab ?? 'modul1') === 'modul3' ? 'active' : '' }}">
        <h2>Tính lương</h2>
        @if($errors->any() && ($activeTab ?? 'modul1') === 'modul3')
            <div style="color:#b83232; margin-bottom: 8px;">
                @foreach($errors->all() as $err)
                    <div>{{ $err }}</div>
                @endforeach
            </div>
        @endif
        @php
            $currentMonth = date('m');
            $currentYear = date('Y');
            $monthValue = old('month', $calcMonth ?: '');
            $yearValue = old('year', $calcYear ?: '');
        @endphp
        <form method="POST" action="{{ route('chamcong.admin.salary.calculate', ['tab' => 'modul3']) }}">
            @csrf
            <label>Tháng: </label>
            <input type="number" name="month" value="{{ $monthValue }}" placeholder="{{ $currentMonth }}" min="1" max="12" style="width:60px;">
            <label>Năm: </label>
            <input type="number" name="year" value="{{ $yearValue }}" placeholder="{{ $currentYear }}" min="2000" max="2100" style="width:70px;">
            <button class="dat-mau-vang" type="submit">Tính lương</button>
        </form>

        @if(!empty($calcResult))
            <h3 id="bang-luong" style="color: #b83232; border-bottom: 2px solid #b83232;">
                Kết quả lương tháng {{ $calcMonth }}/{{ $calcYear }}:
            </h3>
            <table border="1" cellpadding="5" cellspacing="0">
                <tr style="color: white;">
                    <th style="background-color: #b83232;">ID</th>
                    <th style="background-color: #b83232;">Tài khoản</th>
                    <th style="background-color: #b83232;">Loại NV</th>
                    <th style="background-color: #b83232;">Tổng phút</th>
                    <th style="background-color: #b83232;">Tổng giờ</th>
                    <th style="background-color: #b83232;">Lương</th>
                </tr>
                @php $totalSalary = 0; @endphp
                @foreach($calcResult as $res)
                    @php $totalSalary += $res['salary']; @endphp
                    <tr>
                        <td>{{ $res['user_id'] }}</td>
                        <td>{{ $res['username'] }}</td>
                        <td>{{ $res['employee_type'] }}</td>
                        <td>{{ $res['total_mins'] }}</td>
                        <td>{{ round($res['actual_hours'], 2) }}</td>
                        <td>{{ number_format($res['salary']) }} VNĐ</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="5" style="text-align:right; font-weight:bold; color:#b83232;">
                        Tổng lương nhân viên:
                    </td>
                    <td style="font-weight:bold; color:#b83232; font-size:20px;">
                        {{ number_format($totalSalary) }} VNĐ
                    </td>
                </tr>
            </table>
        @endif
        @if(empty($calcResult) && !empty($calcMonth) && !empty($calcYear))
            <p style="color:#b83232; margin-top: 8px;">
                Không có dữ liệu lương cho tháng {{ $calcMonth }}/{{ $calcYear }}.
            </p>
        @endif
    </div>

    <div id="modul4" class="tab-content {{ ($activeTab ?? 'modul1') === 'modul4' ? 'active' : '' }}">
        <h2 id="table-chamcong">Quản lý chấm công</h2>

        <form method="GET" action="{{ route('chamcong.admin.dashboard') }}#table-chamcong" style="margin-bottom: 1rem;" class="filter-row" id="modul4FilterForm">
            <input type="hidden" name="tab" value="modul4">
            <label>Chọn nhân viên:</label>
            <select name="filter_user_id">
                <option value="">-- Tất cả --</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ $filterUID == $u->id ? 'selected' : '' }}>
                        {{ $u->username }}
                    </option>
                @endforeach
            </select>

            <label>Khoảng ngày:</label>
            <input type="text" id="daterange" style="width:200px; text-align:center;"
                   value="{{ $startDateDmy }} - {{ $endDateDmy }}" readonly />

            <input type="hidden" name="start_date" id="start_date" value="{{ $startDate }}">
            <input type="hidden" name="end_date" id="end_date" value="{{ $endDate }}">

            <div class="rows-per-page-group">
                <label>Số hàng/trang:</label>
                <select name="rows_per_page">
                    <option value="10" {{ $rowsPerPage == 10 ? 'selected' : '' }}>10</option>
                    <option value="20" {{ $rowsPerPage == 20 ? 'selected' : '' }}>20</option>
                    <option value="30" {{ $rowsPerPage == 30 ? 'selected' : '' }}>30</option>
                </select>
            </div>
        </form>

        <table border="1" cellpadding="5" cellspacing="0">
            <tr>
                <th class="cc-attendance-th">Nhân viên</th>
                <th class="cc-attendance-th">Ngày chấm công</th>
                <th class="cc-attendance-th">Giờ Check-In</th>
                <th class="cc-attendance-th">Giờ Check-Out</th>
                <th class="cc-attendance-th">Thời gian làm việc</th>
                <th class="cc-attendance-th">Hành động</th>
            </tr>
            <tbody id="attendanceRows">
                @include('chamcong.admin.partials.attendance_rows', ['groupedAtt' => $groupedAtt])
            </tbody>
        </table>
        <div class="detail-link-wrapper">
            <a class="btn btn-detail" id="attendanceDetailLink" href="{{ route('chamcong.admin.detail', ['filter_user_id' => $filterUID, 'start_date' => $startDate, 'end_date' => $endDate, 'rows_per_page' => $rowsPerPage]) }}">
                Xem chi tiết nhân viên & lương
            </a>
        </div>
        <div class="pagination" id="attendancePagination" data-page="{{ $page }}" data-total="{{ $totalPages }}" data-rows="{{ $rowsPerPage }}">
            @include('chamcong.admin.partials.attendance_pagination', [
                'page' => $page,
                'totalPages' => $totalPages,
                'filterUID' => $filterUID,
                'rowsPerPage' => $rowsPerPage,
                'startDate' => $startDate,
                'endDate' => $endDate,
            ])
        </div>
    </div>

    <div id="modul5" class="tab-content {{ ($activeTab ?? 'modul1') === 'modul5' ? 'active' : '' }}">
        <h2>Chấm công bù</h2>
        <form method="POST" action="{{ route('chamcong.admin.attendance.add') }}" class="form-add-attendance">
            @csrf
            <table>
                <tr>
                    <td><label>Chọn nhân viên:</label></td>
                    <td>
                        <select name="user_id" required>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->username }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><label>Thời gian:</label></td>
                    <td><input type="date" name="the_date" required></td>
                </tr>
                <tr>
                    <td><label>Giờ check-in:</label></td>
                    <td><input type="time" name="in_time" required></td>
                    <td><label>Giờ check-out:</label></td>
                    <td><input type="time" name="out_time" required></td>
                </tr>
            </table>
            <button type="submit">Thêm</button>
        </form>
        <div class="proposal-section">
            <h3>Đề xuất của nhân viên</h3>
            @if($compRequests->isEmpty())
                <p class="proposal-empty">Chưa có đề xuất.</p>
            @else
                <table class="proposal-table">
                    <tr>
                        <th>Nhân viên</th>
                        <th>Ngày</th>
                        <th>Giờ check-in</th>
                        <th>Giờ check-out</th>
                        <th>Tổng giờ</th>
                        <th>Hành động</th>
                    </tr>
                    @foreach($compRequests as $req)
                        @php
                            $reqDate = $req->work_date ?? '';
                            $reqDmy = $reqDate ? implode('/', array_reverse(explode('-', $reqDate))) : '';
                            $reqIn = $req->check_in ? substr(explode(' ', $req->check_in)[1] ?? '', 0, 5) : '';
                            $reqOut = $req->check_out ? substr(explode(' ', $req->check_out)[1] ?? '', 0, 5) : '';
                            $reqHours = isset($req->total_minutes) ? round(((int) $req->total_minutes) / 60, 2) : '';
                        @endphp
                        <tr>
                            <td>{{ $req->username }}</td>
                            <td>{{ $reqDmy }}</td>
                            <td>{{ $reqIn }}</td>
                            <td>{{ $reqOut }}</td>
                            <td>{{ $reqHours }}</td>
                            <td class="proposal-actions">
                                <form method="POST" action="{{ route('chamcong.admin.attendance-requests.approve') }}" onsubmit="return confirm('Phê duyệt đề xuất này?');">
                                    @csrf
                                    <input type="hidden" name="request_id" value="{{ $req->id }}">
                                    <button type="submit" class="btn-approve">Phê duyệt</button>
                                </form>
                                <form method="POST" action="{{ route('chamcong.admin.attendance-requests.reject') }}" onsubmit="return confirm('Từ chối đề xuất này?');">
                                    @csrf
                                    <input type="hidden" name="request_id" value="{{ $req->id }}">
                                    <button type="submit" class="btn-reject">Từ chối</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </table>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  function activateTab(btn) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(tc => tc.classList.remove('active'));
    btn.classList.add('active');
    const target = btn.getAttribute('data-target');
    document.getElementById(target).classList.add('active');
    localStorage.setItem('activeTab', target);
  }

  document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => activateTab(btn));
  });

  const tabsEl = document.querySelector('.tabs');
  let initial = tabsEl ? tabsEl.getAttribute('data-initial-tab') : null;
  const hash = window.location.hash.replace('#','');
  if (hash && /^modul[1-5]$/.test(hash) && document.getElementById(hash)) {
    initial = hash;
  } else if (!initial) {
    const saved = localStorage.getItem('activeTab');
    if (saved && document.getElementById(saved)) {
      initial = saved;
    }
  }
  if (initial) {
    const btn = document.querySelector(`.tab-btn[data-target="${initial}"]`);
    if (btn) activateTab(btn);
  }

  const addSelect = document.querySelector('.form-add-employee select[name="employee_type"]');
  if (addSelect) {
    toggleAddUser(addSelect);
    addSelect.addEventListener('change', () => toggleAddUser(addSelect));
  }

  document.querySelectorAll('table tr select[name="employee_type"]').forEach(s => {
    toggleFields(s);
    s.addEventListener('change', () => toggleFields(s));
  });
});

function toggleAddUser(sel) {
  const baseNew = document.querySelector('.form-add-employee .base_new');
  const reqNew  = document.querySelector('.form-add-employee .req_new');
  const hrNew   = document.querySelector('.form-add-employee .hr_new');
  if (!baseNew || !reqNew || !hrNew) return;

  if (sel.value === 'chinh_thuc') {
    baseNew.readOnly = false; reqNew.readOnly = false; hrNew.readOnly = true;
    baseNew.classList.remove('disabled');
    reqNew.classList.remove('disabled');
    hrNew.classList.add('disabled');
  } else {
    baseNew.readOnly = true;  reqNew.readOnly = true;  hrNew.readOnly = false;
    baseNew.classList.add('disabled');
    reqNew.classList.add('disabled');
    hrNew.classList.remove('disabled');
  }
}

function toggleFields(selectEl) {
  const tr = selectEl.closest('tr');
  const base    = tr.querySelector('.base_salary');
  const req     = tr.querySelector('.required_hours');
  const hourly  = tr.querySelector('.hourly_rate');
  if (!base || !req || !hourly) return;

  if (selectEl.value === 'chinh_thuc') {
    base.readOnly   = false; req.readOnly    = false; hourly.readOnly = true;
    base.classList.remove('disabled');
    req.classList.remove('disabled');
    hourly.classList.add('disabled');
  } else {
    base.readOnly   = true;  req.readOnly    = true;  hourly.readOnly = false;
    base.classList.add('disabled');
    req.classList.add('disabled');
    hourly.classList.remove('disabled');
  }
}

window.addEventListener('load', function(){
  var $ = window.jQuery;
  if (!$) return;
  var phpStart = '{{ $startDate }}';
  var phpEnd   = '{{ $endDate }}';

  var filterForm = document.getElementById('modul4FilterForm');
  var userSelect = filterForm ? filterForm.querySelector('select[name="filter_user_id"]') : null;
  var rowsSelect = filterForm ? filterForm.querySelector('select[name="rows_per_page"]') : null;
  var startInput = document.getElementById('start_date');
  var endInput = document.getElementById('end_date');
  var daterangeInput = document.getElementById('daterange');
  var rowsEl = document.getElementById('attendanceRows');
  var paginationEl = document.getElementById('attendancePagination');
  var detailLink = document.getElementById('attendanceDetailLink');
  var baseUrl = "{{ route('chamcong.admin.dashboard') }}";

  function buildUrl(page, ajax) {
    var params = new URLSearchParams();
    if (ajax) params.set('ajax', '1');
    params.set('tab', 'modul4');
    params.set('page', page);
    if (rowsSelect && rowsSelect.value) params.set('rows_per_page', rowsSelect.value);
    if (userSelect && userSelect.value) params.set('filter_user_id', userSelect.value);
    if (startInput && startInput.value) params.set('start_date', startInput.value);
    if (endInput && endInput.value) params.set('end_date', endInput.value);
    var qs = params.toString();
    return baseUrl + (qs ? ('?' + qs) : '');
  }

  function getPageList(page, totalPages) {
    if (totalPages <= 5) {
      return Array.from({ length: totalPages }, function(_, i){ return i + 1; });
    }
    var last = totalPages;
    var list = [];
    if (page <= 3) {
      list = [1, 2, 3, 4, last];
    } else if (page >= last - 1) {
      list = [1, last - 3, last - 2, last - 1, last];
    } else {
      list = [1, page - 1, page, page + 1, last];
    }
    return Array.from(new Set(list.filter(function(n){ return n >= 1 && n <= last; })))
      .sort(function(a, b){ return a - b; });
  }

  function buildPagination(page, totalPages) {
    if (!paginationEl) return;
    var pages = getPageList(page, totalPages);
    var html = '';
    pages.forEach(function(i){
      var active = i === page ? 'active' : '';
      var href = buildUrl(i, false) + '#table-chamcong';
      html += '<a href="' + href + '" data-page="' + i + '" class="' + active + '">' + i + '</a>';
    });
    paginationEl.innerHTML = html;
  }

  function applyFilters(page) {
    if (!rowsEl || !paginationEl) {
      window.location.href = buildUrl(page, false) + '#table-chamcong';
      return;
    }

    var url = buildUrl(page, true);
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function(res){ return res.ok ? res.json() : Promise.reject(res); })
      .then(function(data){
        if (typeof data.rowsHtml !== 'undefined') rowsEl.innerHTML = data.rowsHtml;
        if (data.detailUrl && detailLink) detailLink.href = data.detailUrl;
        if (data.rowsPerPage && rowsSelect) rowsSelect.value = data.rowsPerPage;
        if (userSelect) userSelect.value = (data.filterUID && data.filterUID > 0) ? String(data.filterUID) : '';
        if (data.startDate && startInput) startInput.value = data.startDate;
        if (data.endDate && endInput) endInput.value = data.endDate;
        if (daterangeInput && data.startDateDmy && data.endDateDmy) {
          daterangeInput.value = data.startDateDmy + ' - ' + data.endDateDmy;
        }
        var newPage = data.page || page;
        var newTotal = data.totalPages || 1;
        if (paginationEl) {
          paginationEl.dataset.page = newPage;
          paginationEl.dataset.total = newTotal;
          paginationEl.dataset.rows = data.rowsPerPage || '';
        }
        buildPagination(newPage, newTotal);
        if (window.history && window.history.replaceState) {
          window.history.replaceState({}, '', buildUrl(newPage, false) + '#table-chamcong');
        }
      })
      .catch(function(){
        window.location.href = buildUrl(page, false) + '#table-chamcong';
      });
  }

  if (userSelect) {
    userSelect.addEventListener('change', function(){ applyFilters(1); });
  }
  if (rowsSelect) {
    rowsSelect.addEventListener('change', function(){ applyFilters(1); });
  }

  if (paginationEl) {
    paginationEl.addEventListener('click', function(e){
      var link = e.target.closest('a[data-page]');
      if (!link) return;
      e.preventDefault();
      var page = parseInt(link.dataset.page || '1', 10) || 1;
      applyFilters(page);
    });
  }
  if (paginationEl) {
    var initialPage = parseInt(paginationEl.dataset.page || '1', 10) || 1;
    var initialTotal = parseInt(paginationEl.dataset.total || '1', 10) || 1;
    buildPagination(initialPage, initialTotal);
  }

  $('#daterange').daterangepicker({
    autoUpdateInput: true,
    startDate: moment(phpStart, 'YYYY-MM-DD'),
    endDate:   moment(phpEnd,   'YYYY-MM-DD'),
    locale: {
      format: 'DD/MM/YYYY',
      separator: ' - ',
      applyLabel: 'OK',
      cancelLabel: 'Hủy',
      daysOfWeek:  ['CN','T2','T3','T4','T5','T6','T7'],
      monthNames:  ['Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6','Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12'],
      firstDay: 1
    }
  },
  function(start, end) {
    $('#daterange').val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
    $('#start_date').val(start.format('YYYY-MM-DD'));
    $('#end_date').val(end.format('YYYY-MM-DD'));
    applyFilters(1);
  });
  var drpInstance = $('#daterange').data('daterangepicker');
  if (drpInstance && drpInstance.container) {
    drpInstance.container.addClass('cc-admin');
  }

  $('#daterange').on('show.daterangepicker', function(ev, picker) {
    if (picker && picker.container) {
      picker.container.addClass('cc-open cc-admin');
    }
    setTimeout(function(){
      if (!picker.container.find('.btn-today').length) {
        picker.container.find('.drp-buttons').prepend(`
          <button type="button" class="btn btn-sm btn-primary btn-today">Hôm nay</button>
        `);
        picker.container.find('.btn-today').on('click', function(){
          var now = moment();
          picker.setStartDate(now);
          picker.setEndDate(now);
          picker.leftCalendar.month = now.clone();
          picker.rightCalendar.month = now.clone().add(1, 'month');
          picker.updateCalendars();
        });
      }
    }, 50);
  });
  $('#daterange').on('hide.daterangepicker', function(ev, picker) {
    if (picker && picker.container) {
      picker.container.removeClass('cc-open');
    }
  });
});

document.addEventListener('DOMContentLoaded', function() {
  var overlay = document.getElementById('flashOverlay');
  var popup = document.getElementById('flashPopup');
  if (!overlay || !popup) return;
  var hidden = false;
  function hideFlash() {
    if (hidden) return;
    hidden = true;
    overlay.classList.add('flash-hide');
    setTimeout(function(){ if (overlay && overlay.parentNode) overlay.parentNode.removeChild(overlay); }, 300);
  }
  overlay.addEventListener('click', function(e){
    if (!popup.contains(e.target)) hideFlash();
  });
  setTimeout(hideFlash, 3000);
});
</script>
</body>
</html>










