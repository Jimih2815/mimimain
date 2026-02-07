<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Chi Ti&#7871;t Nh&#226;n Vi&#234;n &amp; L&#432;&#417;ng</title>
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
    <a href="{{ route('chamcong.admin.dashboard') }}">Tr&#7903; v&#7873; Trang Ch&#7845;m c&#244;ng</a>
  </div>
  <h1>Chi Ti&#7871;t Nh&#226;n Vi&#234;n &amp; L&#432;&#417;ng</h1>

  <form method="GET" action="{{ route('chamcong.admin.detail') }}" class="filter-row" style="margin-bottom:1rem;" id="detailFilterForm">
    <label>Ch&#7885;n nh&#226;n vi&#234;n:</label>
    <select name="filter_user_id">
      <option value="">-- T&#7845;t c&#7843; --</option>
      @foreach($allUsers as $u)
        <option value="{{ $u->id }}" {{ $filterUID == $u->id ? 'selected' : '' }}>
          {{ $u->username }}
        </option>
      @endforeach
    </select>

    <label style="margin-left:10px;">Kho&#7843;ng ng&#224;y:</label>
    <input type="text" id="daterange" style="width:200px; text-align:center;"
           value="{{ $startDateDmy.' - '.$endDateDmy }}" readonly />

    <input type="hidden" name="start_date" id="start_date" value="{{ $startDate }}">
    <input type="hidden" name="end_date" id="end_date" value="{{ $endDate }}">

    <label style="margin-left:10px;">S&#7889; h&#224;ng/trang:</label>
    <select name="rows_per_page">
      <option value="10" {{ $rowsPerPage==10 ? 'selected' : '' }}>10</option>
      <option value="20" {{ $rowsPerPage==20 ? 'selected' : '' }}>20</option>
      <option value="30" {{ $rowsPerPage==30 ? 'selected' : '' }}>30</option>
    </select>

    <label style="margin-left:10px;">Xem l&#7883;ch/nv:</label>
    <div class="calendar-wrap" id="detailCalendarWrap">
      <button type="button" class="calendar-toggle" id="detailCalendarToggle" aria-label="L&#7883;ch">
        <i class="fa-solid fa-calendar" aria-hidden="true"></i>
      </button>
      <span class="calendar-hint" id="detailCalendarHint">vui l&#242;ng ch&#7885;n nh&#226;n vi&#234;n</span>
      <div class="calendar-popover" id="detailCalendarPopover" aria-hidden="true">
        <div class="calendar-header">
          {{-- <button type="button" class="cal-nav" id="detailCalPrev" aria-label="Tháng trước">‹</button> --}}
          <div class="calendar-title" id="detailCalendarTitle"></div>
          {{-- <button type="button" class="cal-nav" id="detailCalNext" aria-label="Tháng sau">›</button> --}}
        </div>
        <div class="calendar-weekdays">
          <span>T2</span><span>T3</span><span>T4</span><span>T5</span><span>T6</span><span>T7</span><span>CN</span>
        </div>
        <div class="calendar-grid" id="detailCalendarGrid"></div>
      </div>
    </div>
  </form>

  <table class="day-ne" border="1" cellpadding="5" cellspacing="0">
    <tr>
      <th>Nh&#226;n vi&#234;n</th>
      <th>Ng&#224;y ch&#7845;m c&#244;ng</th>
      <th>Gi&#7901; Check-In</th>
      <th>Gi&#7901; Check-Out</th>
      <th>Th&#7901;i gian l&#224;m</th>
    </tr>    <tbody id="detailRows">
      @include('chamcong.admin.partials.detail_rows', ['groupedAtt' => $groupedAtt])
    </tbody>
  </table>

  <div class="pagination" id="detailPagination" data-page="{{ $page }}" data-total="{{ $totalPages }}" data-rows="{{ $rowsPerPage }}">
    @if($page > 1)
      <a href="{{ route('chamcong.admin.detail', ['filter_user_id' => $filterUID, 'start_date' => $startDate, 'end_date' => $endDate, 'rows_per_page' => $rowsPerPage, 'page' => $page-1]) }}">&laquo;</a>
    @endif
    @for($i = 1; $i <= $totalPages; $i++)
      <a href="{{ route('chamcong.admin.detail', ['filter_user_id' => $filterUID, 'start_date' => $startDate, 'end_date' => $endDate, 'rows_per_page' => $rowsPerPage, 'page' => $i]) }}" class="{{ $i == $page ? 'active' : '' }}">{{ $i }}</a>
    @endfor
    @if($page < $totalPages)
      <a href="{{ route('chamcong.admin.detail', ['filter_user_id' => $filterUID, 'start_date' => $startDate, 'end_date' => $endDate, 'rows_per_page' => $rowsPerPage, 'page' => $page+1]) }}">&raquo;</a>
    @endif
  </div>

  @php
    $sumHoursDisplay = round($sumHours, 2);
    $sumSalaryDisplay = number_format($sumSalary, 0, ',', '.');
  @endphp
  <div style="max-width: 400px; margin-top: 30px;">
    <table border="1" cellpadding="6" cellspacing="0" width="100%">
      <tr>
        <th style="text-align:right; padding-right:0.5rem;">T&#7893;ng gi&#7901; l&#224;m:</th>
        <td id="totalHours">{{ $sumHoursDisplay }}</td>
      </tr>
      <tr>
        <th style="text-align:right; padding-right:0.5rem;">T&#7893;ng l&#432;&#417;ng:</th>
        <td id="totalSalary">{{ $sumSalaryDisplay }}</td>
      </tr>
      <tr>
        <th style="text-align:right; padding-right:0.5rem;">Th&#432;&#7903;ng:</th>
        <td><input style="border: 0px solid #ccc;" type="text" id="bonusInput" value="0"></td>
      </tr>
      <tr>
        <th style="text-align:right; background-color:#c82333; padding-right:0.5rem; color:white;">T&#7893;ng c&#7897;ng l&#432;&#417;ng:</th>
        <td><input style="border: 0px solid #ccc; color: #c82333; font-weight:800;" type="text" id="finalInput" value="{{ $sumSalaryDisplay }}"></td>
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

  var calendarDays = @json($calendarDays ?? []);
  var calendarToggle = document.getElementById('detailCalendarToggle');
  var calendarPopover = document.getElementById('detailCalendarPopover');
  var calendarGrid = document.getElementById('detailCalendarGrid');
  var calendarTitle = document.getElementById('detailCalendarTitle');
  var calPrev = document.getElementById('detailCalPrev');
  var calNext = document.getElementById('detailCalNext');
  var calendarHint = document.getElementById('detailCalendarHint');
  var monthNames = ['Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6','Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12'];
  var monthList = [];
  var currentMonth = '';

  function getMonthList(startStr, endStr) {
    if (!startStr || !endStr) return [];
    var s = startStr.split('-');
    var e = endStr.split('-');
    if (s.length < 2 || e.length < 2) return [];
    var cur = new Date(parseInt(s[0],10), parseInt(s[1],10) - 1, 1);
    var end = new Date(parseInt(e[0],10), parseInt(e[1],10) - 1, 1);
    if (isNaN(cur.getTime()) || isNaN(end.getTime())) return [];
    var list = [];
    while (cur <= end) {
      var y = cur.getFullYear();
      var m = String(cur.getMonth() + 1).padStart(2, '0');
      list.push(y + '-' + m);
      cur.setMonth(cur.getMonth() + 1);
    }
    return list;
  }

  function pickClosestMonth(list) {
    if (!list || !list.length) return '';
    var now = new Date();
    var nowKey = now.getFullYear() * 12 + now.getMonth();
    var best = list[0];
    var bestDiff = 999999;
    list.forEach(function(m){
      var parts = m.split('-');
      if (parts.length < 2) return;
      var key = parseInt(parts[0],10) * 12 + (parseInt(parts[1],10) - 1);
      var diff = Math.abs(key - nowKey);
      if (diff < bestDiff) {
        bestDiff = diff;
        best = m;
      }
    });
    return best;
  }

  function buildCalendarMap(days, monthStr) {
    var map = {};
    if (!Array.isArray(days) || !monthStr) return map;
    var prefix = monthStr + '-';
    days.forEach(function(d){
      if (!d || !d.date) return;
      if (String(d.date).indexOf(prefix) !== 0) return;
      var day = parseInt(String(d.date).slice(8, 10), 10);
      if (!isNaN(day)) map[day] = d.status || '';
    });
    return map;
  }

  function renderCalendar(monthStr) {
    if (!calendarGrid || !calendarTitle || !monthStr) return;
    var parts = monthStr.split('-');
    var year = parseInt(parts[0], 10);
    var month = parseInt(parts[1], 10);
    if (!year || !month) return;

    calendarTitle.textContent = monthNames[month - 1] + ' ' + year;
    var firstDay = (new Date(year, month - 1, 1).getDay() + 6) % 7;
    var daysInMonth = new Date(year, month, 0).getDate();
    var calendarData = buildCalendarMap(calendarDays, monthStr);

    var html = '';
    for (var i = 0; i < firstDay; i++) {
      html += '<div class="cal-cell empty"></div>';
    }
    for (var d = 1; d <= daysInMonth; d++) {
      var status = calendarData[d];
      var cls = '';
      if (status === 'complete') cls = 'complete';
      if (status === 'incomplete') cls = 'partial';
      html += '<div class="cal-cell"><span class="cal-day ' + cls + '">' + d + '</span></div>';
    }
    calendarGrid.innerHTML = html;
  }

  function updateCalendarNav() {
    if (!calPrev || !calNext) return;
    var idx = monthList.indexOf(currentMonth);
    calPrev.disabled = idx <= 0;
    calNext.disabled = idx < 0 || idx >= monthList.length - 1;
  }

  function refreshCalendarFromRange() {
    monthList = getMonthList(startInput ? startInput.value : '', endInput ? endInput.value : '');
    if (!monthList.length) {
      var now = new Date();
      var cur = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0');
      monthList = [cur];
    }
    currentMonth = pickClosestMonth(monthList) || monthList[0];
    renderCalendar(currentMonth);
    updateCalendarNav();
  }

  function setCalendarEnabled() {
    var enabled = !!(userSelect && userSelect.value);
    if (calendarToggle) {
      calendarToggle.classList.toggle('is-disabled', !enabled);
      calendarToggle.setAttribute('aria-disabled', enabled ? 'false' : 'true');
    }
    if (!enabled && calendarPopover) {
      calendarPopover.classList.remove('is-open');
    }
  }

  function showCalendarHint() {
    if (!calendarHint) return;
    calendarHint.classList.add('is-show');
    clearTimeout(calendarHint._hideTimer);
    calendarHint._hideTimer = setTimeout(function(){
      calendarHint.classList.remove('is-show');
    }, 2000);
  }
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
        if (Array.isArray(data.calendarDays)) {
          calendarDays = data.calendarDays;
        }
        refreshCalendarFromRange();
        setCalendarEnabled();
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
    userSelect.addEventListener('change', function(){ setCalendarEnabled(); applyFilters(1); });
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

    if (calendarToggle) {
    calendarToggle.addEventListener('click', function(e){
      e.stopPropagation();
      if (!(userSelect && userSelect.value)) {
        showCalendarHint();
        return;
      }
      if (calendarPopover) {
        calendarPopover.classList.toggle('is-open');
      }
    });
  }
  if (calendarPopover) {
    calendarPopover.addEventListener('click', function(e){ e.stopPropagation(); });
  }
  document.addEventListener('click', function(){
    if (calendarPopover) calendarPopover.classList.remove('is-open');
  });

  if (calPrev) {
    calPrev.addEventListener('click', function(){
      var idx = monthList.indexOf(currentMonth);
      if (idx > 0) {
        currentMonth = monthList[idx - 1];
        renderCalendar(currentMonth);
        updateCalendarNav();
      }
    });
  }
  if (calNext) {
    calNext.addEventListener('click', function(){
      var idx = monthList.indexOf(currentMonth);
      if (idx >= 0 && idx < monthList.length - 1) {
        currentMonth = monthList[idx + 1];
        renderCalendar(currentMonth);
        updateCalendarNav();
      }
    });
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
  return str.replace(/[^\d]/g, "");
}
function parseMoney(val) {
  if (typeof val === 'number') return val;
  if (val === null || typeof val === 'undefined') return 0;
  var raw = unformatMoney(String(val));
  return parseFloat(raw) || 0;
}
function formatInputValue(el) {
  if (!el) return 0;
  var raw = unformatMoney(el.value || '');
  var num = parseFloat(raw) || 0;
  el.value = formatMoney(num);
  return num;
}
var detailTotalSalary = 0;

function setDetailTotalSalary(val, formatted) {
  detailTotalSalary = parseMoney(val);
  var totalSalaryEl = document.getElementById('totalSalary');
  if (totalSalaryEl) {
    totalSalaryEl.textContent = formatted || formatMoney(detailTotalSalary);
  }
  var bonusEl = document.getElementById('bonusInput');
  var finalEl = document.getElementById('finalInput');
  if (bonusEl && finalEl) {
    var bonusVal = parseMoney(bonusEl.value);
    finalEl.value = formatMoney(detailTotalSalary + bonusVal);
  }
}

document.addEventListener('DOMContentLoaded', function(){
  var tsEl = document.getElementById('totalSalary');
  var tsText = tsEl ? tsEl.textContent : '0';
  setDetailTotalSalary(parseMoney(tsText) || 0);

  var bonusEl = document.getElementById('bonusInput');
  var finalEl = document.getElementById('finalInput');
  if (!bonusEl || !finalEl) return;

  bonusEl.addEventListener('input', function(){
    var bonusVal = formatInputValue(bonusEl);
    var total = detailTotalSalary + bonusVal; 
    finalEl.value = formatMoney(total);
  });

  finalEl.addEventListener('input', function(){
    var finalVal = formatInputValue(finalEl);
    var bonusVal = finalVal - detailTotalSalary;
    bonusEl.value = formatMoney(bonusVal);
  });
});
</script>
</body>
</html>


































