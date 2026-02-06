<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Detail Nhân Viên & Lương</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @vite([
      'resources/chamcong/admin.css',
      'resources/chamcong/vendor/daterangepicker.min.css',
      'resources/chamcong/vendor.js',
  ])
  <style>
    .tro-ve-trang-admin > a {
       display: inline-block;
       background-color: #d1a029;
       color: #fff;
       padding: 8px 12px;
       border-radius: 5px;
       text-decoration: none;
    }
    .tro-ve-trang-admin {
      text-align: right;
      margin: 1rem 0;
      display: flex;
      justify-content: flex-end;
    }
  </style>
</head>

<body>
@include('chamcong.partials.admin_navbar')
<div class="container">
  <div class="tro-ve-trang-admin">
    <a href="{{ route('chamcong.admin.dashboard') }}">Trở về Trang Admin</a>
  </div>
  <h1>Chi Tiết Nhân Viên & Lương</h1>

  <form method="GET" action="{{ route('chamcong.admin.detail') }}" class="filter-row" style="margin-bottom:1rem;">
    <label>Chọn nhân viên:</label>
    <select name="filter_user_id" onchange="this.form.submit()">
      <option value="">-- Tất cả --</option>
      @foreach($allUsers as $u)
        <option value="{{ $u->id }}" {{ $filterUID == $u->id ? 'selected' : '' }}>
          {{ $u->username }}
        </option>
      @endforeach
    </select>

    <label style="margin-left:10px;">Khoảng ngày:</label>
    <input type="text" id="daterange" style="width:200px; text-align:center;"
           value="{{ $startDateDmy.' - '.$endDateDmy }}" readonly />

    <input type="hidden" name="start_date" id="start_date" value="{{ $startDate }}">
    <input type="hidden" name="end_date" id="end_date" value="{{ $endDate }}">

    <label style="margin-left:10px;">Số hàng/trang:</label>
    <select name="rows_per_page" onchange="this.form.submit()">
      <option value="10" {{ $rowsPerPage==10 ? 'selected' : '' }}>10</option>
      <option value="20" {{ $rowsPerPage==20 ? 'selected' : '' }}>20</option>
      <option value="30" {{ $rowsPerPage==30 ? 'selected' : '' }}>30</option>
    </select>
  </form>

  <table class="day-ne" border="1" cellpadding="5" cellspacing="0">
    <tr>
      <th>Nhân viên</th>
      <th>Ngày chấm công</th>
      <th>Giờ Check-In</th>
      <th>Giờ Check-Out</th>
      <th>Thời gian làm</th>
    </tr>

    @if(count($groupedAtt) === 0)
      <tr>
        <td colspan="5" style="color:red; font-weight:bold;">
          Không có dữ liệu chấm công
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
          $dmy = $g->work_date ? implode('/', array_reverse(explode('-', $g->work_date))) : '';
        @endphp
        <tr>
          <td>{{ $g->username }}</td>
          <td>{{ $dmy }}</td>
          <td>{{ $earliestHM }}</td>
          <td>{{ $latestHM }}</td>
          <td>{{ $dailyHours }} giờ</td>
        </tr>
      @endforeach
    @endif
  </table>

  <div class="pagination">
    @if($page > 1)
      <a href="{{ route('chamcong.admin.detail', ['filter_user_id' => $filterUID, 'start_date' => $startDate, 'end_date' => $endDate, 'rows_per_page' => $rowsPerPage, 'page' => $page-1]) }}">«</a>
    @endif
    @for($i = 1; $i <= $totalPages; $i++)
      <a href="{{ route('chamcong.admin.detail', ['filter_user_id' => $filterUID, 'start_date' => $startDate, 'end_date' => $endDate, 'rows_per_page' => $rowsPerPage, 'page' => $i]) }}" class="{{ $i == $page ? 'active' : '' }}">{{ $i }}</a>
    @endfor
    @if($page < $totalPages)
      <a href="{{ route('chamcong.admin.detail', ['filter_user_id' => $filterUID, 'start_date' => $startDate, 'end_date' => $endDate, 'rows_per_page' => $rowsPerPage, 'page' => $page+1]) }}">»</a>
    @endif
  </div>

  @php
    $sumHoursDisplay = round($sumHours, 2);
    $sumSalaryDisplay = number_format($sumSalary, 0, ',', '.');
  @endphp
  <div style="max-width: 400px; margin-top: 30px;">
    <table border="1" cellpadding="6" cellspacing="0" width="100%">
      <tr>
        <th style="text-align:right;">Tổng giờ làm:</th>
        <td id="totalHours">{{ $sumHoursDisplay }}</td>
      </tr>
      <tr>
        <th style="text-align:right;">Tổng lương:</th>
        <td id="totalSalary">{{ $sumSalaryDisplay }}</td>
      </tr>
      <tr>
        <th style="text-align:right;">Thưởng:</th>
        <td><input style="border: 0px solid #ccc;" type="text" id="bonusInput" value="0"></td>
      </tr>
      <tr>
        <th style="text-align:right; background-color:#c82333;">Tổng cộng lương:</th>
        <td><input style="border: 0px solid #ccc; color: #c82333; font-weight:800;" type="text" id="finalInput" value="0"></td>
      </tr>
    </table>
  </div>
</div>

<script>
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
          picker.rightCalendar.month= now.clone().add(1,'month');
          picker.updateCalendars();
        });
      }
    }, 50);
  });
});

function formatMoney(value) {
  return value.toString().replace(/\\B(?=(\\d{3})+(?!\\d))/g, ".");
}
function unformatMoney(str) {
  return str.replace(/[^\\d]/g, "");
}
document.addEventListener('DOMContentLoaded', function(){
  var tsText = document.getElementById('totalSalary').textContent;
  tsText = unformatMoney(tsText);
  var totalSalary = parseFloat(tsText) || 0;

  var bonusEl = document.getElementById('bonusInput');
  var finalEl = document.getElementById('finalInput');
  finalEl.value = formatMoney(totalSalary);

  bonusEl.addEventListener('input', function(){
    var bonusRaw = unformatMoney(bonusEl.value);
    var bonusVal = parseFloat(bonusRaw) || 0;
    var total = totalSalary + bonusVal; 
    finalEl.value = formatMoney(total);
  });

  finalEl.addEventListener('input', function(){
    var finalRaw = unformatMoney(finalEl.value);
    var finalVal = parseFloat(finalRaw) || 0;
    var bonusVal = finalVal - totalSalary;
    bonusEl.value = formatMoney(bonusVal);
  });
});
</script>
</body>
</html>
