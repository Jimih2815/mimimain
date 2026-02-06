<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>MiMi Quản Lý Chấm Công</title>
    @vite([
        'resources/chamcong/admin.css',
        'resources/chamcong/vendor/daterangepicker.min.css',
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
    {!! session('chamcong_flash_msg') !!}
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
                            <button type="submit" style="color:red;">Xóa</button>
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
                <tr>
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

        <form method="GET" action="{{ route('chamcong.admin.dashboard') }}#table-chamcong" style="margin-bottom: 1rem;" class="filter-row">
            <label>Chọn nhân viên:</label>
            <select name="filter_user_id" onchange="this.form.submit()">
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

            <label>Số hàng/trang:</label>
            <select name="rows_per_page" onchange="this.form.submit()">
                <option value="10" {{ $rowsPerPage == 10 ? 'selected' : '' }}>10</option>
                <option value="20" {{ $rowsPerPage == 20 ? 'selected' : '' }}>20</option>
                <option value="30" {{ $rowsPerPage == 30 ? 'selected' : '' }}>30</option>
            </select>
        </form>

        <table border="1" cellpadding="5" cellspacing="0">
            <tr>
                <th>Nhân viên</th>
                <th>Ngày chấm công</th>
                <th>Giờ Check-In</th>
                <th>Giờ Check-Out</th>
                <th>Thời gian làm việc</th>
                <th>Hành động</th>
            </tr>

            @if(count($groupedAtt) === 0)
                <tr>
                    <td colspan="6" style="color:red; font-weight:bold;">
                        Không có dữ liệu chấm công tháng này
                    </td>
                </tr>
            @else
                @foreach($groupedAtt as $g)
                    @php
                        $earliestTS = strtotime($g->earliest_in);
                        $latestTS = strtotime($g->latest_out);
                        $dailyHours = 0;
                        if ($earliestTS && $latestTS && $latestTS > $earliestTS) {
                            $dailyHours = round(($latestTS - $earliestTS) / 3600, 2);
                        }
                        $earliestHM = $g->earliest_in ? substr(explode(' ', $g->earliest_in)[1] ?? '', 0, 5) : '';
                        $latestHM = $g->latest_out ? substr(explode(' ', $g->latest_out)[1] ?? '', 0, 5) : '';
                        $workDateDmy = $g->work_date ? implode('/', array_reverse(explode('-', $g->work_date))) : '';
                    @endphp
                    <tr>
                        <td>{{ $g->username }} (user_id={{ $g->user_id }})</td>
                        <td>{{ $workDateDmy }}</td>
                        <form method="POST" action="{{ route('chamcong.admin.attendance.update-earliest-latest') }}">
                            @csrf
                            <input type="hidden" name="earliest_id" value="{{ $g->earliest_id }}">
                            <input type="hidden" name="latest_id" value="{{ $g->latest_id }}">
                            <input type="hidden" name="the_date" value="{{ $g->work_date }}">
                            <td><input type="text" name="earliest_in" value="{{ $earliestHM }}" size="5"></td>
                            <td><input type="text" name="latest_out" value="{{ $latestHM }}" size="5"></td>
                            <td>{{ $dailyHours }} giờ</td>
                            <td class="action-buttons">
                                <button type="submit">Lưu</button>
                        </form>
                                <form method="POST" action="{{ route('chamcong.admin.attendance.delete-day') }}" style="display:inline;"
                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa toàn bộ chấm công ngày {{ $g->work_date }} của user {{ $g->username }}?');">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $g->user_id }}">
                                    <input type="hidden" name="the_date" value="{{ $g->work_date }}">
                                    <button type="submit" style="color:white; background-color:#b83232;">Xóa</button>
                                </form>
                            </td>
                    </tr>
                @endforeach
            @endif
        </table>
        <div class="detail-link-wrapper">
            <a class="btn btn-detail" href="{{ route('chamcong.admin.detail', ['filter_user_id' => $filterUID, 'start_date' => $startDate, 'end_date' => $endDate, 'rows_per_page' => $rowsPerPage]) }}">
                Xem chi tiết nhân viên & lương
            </a>
        </div>
        <div class="pagination">
            @if($page > 1)
                <a href="{{ route('chamcong.admin.dashboard', ['filter_user_id' => $filterUID, 'rows_per_page' => $rowsPerPage, 'page' => $page-1]) }}#table-chamcong">&laquo;</a>
            @endif
            @for($i = 1; $i <= $totalPages; $i++)
                <a href="{{ route('chamcong.admin.dashboard', ['filter_user_id' => $filterUID, 'rows_per_page' => $rowsPerPage, 'page' => $i]) }}#table-chamcong"
                   class="{{ $i == $page ? 'active' : '' }}">{{ $i }}</a>
            @endfor
            @if($page < $totalPages)
                <a href="{{ route('chamcong.admin.dashboard', ['filter_user_id' => $filterUID, 'rows_per_page' => $rowsPerPage, 'page' => $page+1]) }}#table-chamcong">&raquo;</a>
            @endif
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
    $('#daterange').closest('form').submit();
  });

  $('#daterange').on('show.daterangepicker', function(ev, picker) {
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
});
</script>
</body>
</html>
