<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Detail Nhân Viên & Lương</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @vite([
        'resources/chamcong/admin.css',
        'resources/chamcong/vendor.js',
    ])
  <style>
    .tro-ve-trang-admin > a {
       display: inline-block;
       background-color: #d1a029;
       color: #fff;
       padding: 8px 12px;
       border-radius: 50px;
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
    <a href="{{ route('chamcong.admin.dashboard') }}">Trở về Trang Chấm công</a>
  </div>
  <h1>Chi Tiết Nhân Viên & Lương</h1>

  <form method="GET" action="{{ route('chamcong.admin.detail') }}" class="filter-row" style="margin-bottom:1rem;" id="detailFilterForm">
    <label>Chọn nhân viên:</label>
    <select name="filter_user_id">
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
    <select name="rows_per_page">
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
    </tr>    <tbody id="detailRows">
      @include('chamcong.admin.partials.detail_rows', ['groupedAtt' => $groupedAtt])
    </tbody>
  </table>

  <div class="pagination" id="detailPagination" data-page="{{ $page }}" data-total="{{ $totalPages }}" data-rows="{{ $rowsPerPage }}">
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
        <th style="text-align:right; padding-right:0.5rem;">Tổng giờ làm:</th>
        <td id="totalHours">{{ $sumHoursDisplay }}</td>
      </tr>
      <tr>
        <th style="text-align:right; padding-right:0.5rem;">Tổng lương:</th>
        <td id="totalSalary">{{ $sumSalaryDisplay }}</td>
      </tr>
      <tr>
        <th style="text-align:right; padding-right:0.5rem;">Thưởng:</th>
        <td><input style="border: 0px solid #ccc;" type="text" id="bonusInput" value="0"></td>
      </tr>
      <tr>
        <th style="text-align:right; background-color:#c82333; padding-right:0.5rem; color:white;">Tổng cộng lương:</th>
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

  var filterForm = document.getElementById('detailFilterForm');
  var userSelect = filterForm ? filterForm.querySelector('select[name="filter_user_id"]') : null;
  var rowsSelect = filterForm ? filterForm.querySelector('select[name="rows_per_page"]') : null;
  var startInput = document.getElementById('start_date');
  var endInput = document.getElementById('end_date');
  var daterangeInput = document.getElementById('daterange');
  var rowsEl = document.getElementById('detailRows');
  var paginationEl = document.getElementById('detailPagination');
  var totalHoursEl = document.getElementById('totalHours');
  var baseUrl = "{{ route('chamcong.admin.detail') }}";

  function buildUrl(page, ajax) {
    var params = new URLSearchParams();
    if (ajax) params.set('ajax', '1');
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
      var href = buildUrl(i, false);
      html += '<a href="' + href + '" data-page="' + i + '" class="' + active + '">' + i + '</a>';
    });
    paginationEl.innerHTML = html;
  }

  function applyFilters(page) {
    if (!rowsEl || !paginationEl) {
      window.location.href = buildUrl(page, false);
      return;
    }

    var url = buildUrl(page, true);
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function(res){ return res.ok ? res.json() : Promise.reject(res); })
      .then(function(data){
        if (typeof data.rowsHtml !== 'undefined') rowsEl.innerHTML = data.rowsHtml;
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
        if (totalHoursEl && typeof data.sumHoursDisplay !== 'undefined') {
          totalHoursEl.textContent = data.sumHoursDisplay;
        }
        if (typeof data.sumSalary !== 'undefined') {
          var formatted = data.sumSalaryFormatted ? data.sumSalaryFormatted : formatMoney(data.sumSalary || 0);
          setDetailTotalSalary(data.sumSalary || 0, formatted);
        }
        if (window.history && window.history.replaceState) {
          window.history.replaceState({}, '', buildUrl(newPage, false));
        }
      })
      .catch(function(){
        window.location.href = buildUrl(page, false);
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

  $('#daterange').on('show.daterangepicker', function(ev, picker) {
    if (picker && picker.container) {
      picker.container.addClass('cc-open');
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
          picker.rightCalendar.month= now.clone().add(1,'month');
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

function formatMoney(value) {
  var num = Math.round(parseFloat(value) || 0);
  return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}
function unformatMoney(str) {
  return str.replace(/[^\\d]/g, "");
}
var detailTotalSalary = 0;

function setDetailTotalSalary(val, formatted) {
  detailTotalSalary = parseFloat(val) || 0;
  var totalSalaryEl = document.getElementById('totalSalary');
  if (totalSalaryEl) {
    totalSalaryEl.textContent = formatted || formatMoney(detailTotalSalary);
  }
  var bonusEl = document.getElementById('bonusInput');
  var finalEl = document.getElementById('finalInput');
  if (bonusEl && finalEl) {
    var bonusVal = parseFloat(unformatMoney(bonusEl.value)) || 0;
    finalEl.value = formatMoney(detailTotalSalary + bonusVal);
  }
}

document.addEventListener('DOMContentLoaded', function(){
  var tsEl = document.getElementById('totalSalary');
  var tsText = tsEl ? tsEl.textContent : '0';
  tsText = unformatMoney(tsText);
  setDetailTotalSalary(parseFloat(tsText) || 0);

  var bonusEl = document.getElementById('bonusInput');
  var finalEl = document.getElementById('finalInput');
  if (!bonusEl || !finalEl) return;

  bonusEl.addEventListener('input', function(){
    var bonusRaw = unformatMoney(bonusEl.value);
    var bonusVal = parseFloat(bonusRaw) || 0;
    var total = detailTotalSalary + bonusVal; 
    finalEl.value = formatMoney(total);
  });

  finalEl.addEventListener('input', function(){
    var finalRaw = unformatMoney(finalEl.value);
    var finalVal = parseFloat(finalRaw) || 0;
    var bonusVal = finalVal - detailTotalSalary;
    bonusEl.value = formatMoney(bonusVal);
  });
});
</script>
</body>
</html>






