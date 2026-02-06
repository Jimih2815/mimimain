<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông tin cá nhân</title>
    @vite('resources/chamcong/info.css')
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <style>
        table { width: 100%; border-collapse: collapse; margin: 0 0 1rem 0; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        .success { color: green; }
        .error { color: red; }
        .modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); }
        .modal.show { display: block; }
        .modal-content { background-color: #fff; margin: 10% auto; padding: 20px; width: 90%; max-width: 400px; border-radius: 5px; position: relative; }
        .close { position: absolute; right: 15px; top: 10px; cursor: pointer; font-size: 24px; }
        .container { max-width: 1200px; margin: auto; padding: 1rem; }
    </style>
</head>
<body>
<a href="{{ route('chamcong.dashboard') }}" class="logo-link">
    <img src="{{ Vite::asset('resources/chamcong/logo.png') }}" alt="Logo Công Ty" class="company-logo">
</a>

<div class="container">
    <h2>Thông tin của bạn: {{ $username }}</h2>

    <h3>Lịch sử chấm công</h3>
    <div class="table-controls">
        <div class="calendar-wrap">
            <button type="button" class="calendar-toggle" id="calendarToggle" aria-label="Lịch">
                <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
                    <path fill="currentColor" d="M7 2h2v2h6V2h2v2h3v18H4V4h3V2Zm12 6H5v12h14V8Z"/>
                </svg>
            </button>
            <div class="calendar-popover" id="calendarPopover" aria-hidden="true">
                <div class="calendar-header">
                    <button type="button" class="cal-nav" id="calPrev" aria-label="Tháng trước">‹</button>
                    <div class="calendar-title" id="calendarTitle"></div>
                    <button type="button" class="cal-nav" id="calNext" aria-label="Tháng sau">›</button>
                </div>
                <div class="calendar-weekdays">
                    <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                </div>
                <div class="calendar-grid" id="calendarGrid"></div>
            </div>
        </div>
        <label for="monthFilter" class="sr-only">Tháng</label>
        <select id="monthFilter">
            @foreach($monthOptions as $opt)
                <option value="{{ $opt['value'] }}" {{ $selectedMonth === $opt['value'] ? 'selected' : '' }}>
                    {{ $opt['label'] }}
                </option>
            @endforeach
        </select>
        <label for="rowsPerPage" class="sr-only">Hiển thị</label>
        <select id="rowsPerPage">
            <option value="5" {{ $rowsPerPage == 5 ? 'selected' : '' }}>5 ngày</option>
            <option value="10" {{ $rowsPerPage == 10 ? 'selected' : '' }}>10 ngày</option>
            <option value="20" {{ $rowsPerPage == 20 ? 'selected' : '' }}>20 ngày</option>
            <option value="30" {{ $rowsPerPage == 30 ? 'selected' : '' }}>30 ngày</option>
        </select>
    </div>
    <table>
        <tr>
            <th>Ngày</th>
            <th>Giờ Check-In</th>
            <th>Giờ Check-Out</th>
        </tr>
        <tbody id="attendanceRows">
            @foreach($groupedAtt as $g)
                @php
                    $dayString = $g->work_date;
                    $earliestHM = $g->earliest_in ? substr(explode(' ', $g->earliest_in)[1] ?? '', 0, 5) : '';
                    $latestHM = $g->latest_out ? substr(explode(' ', $g->latest_out)[1] ?? '', 0, 5) : '';
                    $dmy = $dayString ? implode('/', array_reverse(explode('-', $dayString))) : '';
                @endphp
                <tr>
                    <td>{{ $dmy }}</td>
                    <td>{{ $earliestHM }}</td>
                    <td>{{ $latestHM }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @php
        $startPage = $page - 1;
        $endPage = $page + 1;
        if ($page <= 2) { $startPage = 1; $endPage = 3; }
        if ($page >= $totalPages - 1) { $startPage = $totalPages - 2; $endPage = $totalPages; }
        if ($startPage < 1) $startPage = 1;
        if ($endPage > $totalPages) $endPage = $totalPages;
    @endphp

    <div class="pagination" id="attendancePagination" data-page="{{ $page }}" data-total="{{ $totalPages }}" data-rows="{{ $rowsPerPage }}" data-month="{{ $selectedMonth }}">
        @for($i = $startPage; $i <= $endPage; $i++)
            <a href="{{ route('chamcong.info', ['page' => $i, 'rows_per_page' => $rowsPerPage, 'month' => $selectedMonth]) }}" data-page="{{ $i }}" class="{{ $i == $page ? 'active' : '' }}">
                {{ $i }}
            </a>
        @endfor
    </div>

    <div class="summary">
        <p class="total-hours">
            <strong>Tổng giờ làm việc:  </strong>
            <span id="totalHoursValue">{{ round($actualHoursThisMonth, 2) }} giờ</span>
        </p>
        <p class="current-salary">
            <strong>Lương hiện tại:  </strong>
            <span id="currentSalaryValue">{{ number_format($currentSalary) }} VNĐ</span>
        </p>
    </div>

    <button class="open-password-modal">Đổi mật khẩu</button>

    <div id="passwordModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Đổi mật khẩu</h3>

            @if(!empty($passMsg))
                <p style="color: {{ $passMsg === 'Đổi mật khẩu thành công!' ? 'green' : 'red' }};">
                    {{ $passMsg }}
                </p>
            @endif

            <form method="POST" action="{{ route('chamcong.info.password') }}">
                @csrf
                <label for="old_password">Mật khẩu cũ:</label>
                <input type="password" name="old_password" required>

                <label for="new_password">Mật khẩu mới:</label>
                <input type="password" name="new_password" required>

                <label for="confirm_new_password">Xác nhận mật khẩu mới:</label>
                <input type="password" name="confirm_new_password" required>

                <button type="submit">Xác nhận đổi mật khẩu</button>
            </form>
        </div>
    </div>

    
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById("passwordModal");
    const btn = document.querySelector(".open-password-modal");
    const closeBtn = document.querySelector(".close");

    if (btn) {
        btn.onclick = function() { modal.classList.add("show"); }
    }
    if (closeBtn) {
        closeBtn.onclick = function() { modal.classList.remove("show"); }
    }
    window.onclick = function(event) {
        if (event.target === modal) { modal.classList.remove("show"); }
    }
    @if(!empty($passMsg) && $passMsg !== "Đổi mật khẩu thành công!")
        modal.classList.add("show");
    @endif
});

document.addEventListener("DOMContentLoaded", function() {
    const paginationEl = document.getElementById('attendancePagination');
    const rowsEl = document.getElementById('attendanceRows');
    const rowsPerPageEl = document.getElementById('rowsPerPage');
    const monthFilterEl = document.getElementById('monthFilter');
    const totalHoursEl = document.getElementById('totalHoursValue');
    const currentSalaryEl = document.getElementById('currentSalaryValue');
    const calendarToggle = document.getElementById('calendarToggle');
    const calendarPopover = document.getElementById('calendarPopover');
    const calendarGrid = document.getElementById('calendarGrid');
    const calendarTitle = document.getElementById('calendarTitle');
    const calPrev = document.getElementById('calPrev');
    const calNext = document.getElementById('calNext');
    if (!paginationEl || !rowsEl || !rowsPerPageEl || !monthFilterEl) return;

    const baseUrl = "{{ route('chamcong.info') }}";

    function getRowsPerPage() {
        return parseInt(rowsPerPageEl.value || paginationEl.dataset.rows || '5', 10) || 5;
    }
    function getMonth() {
        return monthFilterEl.value || paginationEl.dataset.month || '';
    }

    function buildUrl(page) {
        const rpp = getRowsPerPage();
        const month = getMonth();
        return `${baseUrl}?page=${page}&rows_per_page=${rpp}&month=${encodeURIComponent(month)}`;
    }

    function buildCalendarMap(days) {
        const map = {};
        (days || []).forEach(d => { map[d.day] = d.status; });
        return map;
    }

    const monthNames = ['Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6','Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12'];
    let calendarData = buildCalendarMap(@json($calendarDays ?? []));

    function renderCalendar(monthStr) {
        if (!calendarGrid || !calendarTitle) return;
        const parts = monthStr.split('-');
        const year = parseInt(parts[0], 10);
        const month = parseInt(parts[1], 10);
        if (!year || !month) return;

        calendarTitle.textContent = `${monthNames[month - 1]} ${year}`;
        const firstDay = new Date(year, month - 1, 1).getDay();
        const daysInMonth = new Date(year, month, 0).getDate();

        let html = '';
        for (let i = 0; i < firstDay; i++) {
            html += '<div class="cal-cell empty"></div>';
        }
        for (let d = 1; d <= daysInMonth; d++) {
            const status = calendarData[d];
            let cls = '';
            if (status === 'complete') cls = 'complete';
            if (status === 'incomplete') cls = 'partial';
            html += `<div class="cal-cell"><span class="cal-day ${cls}">${d}</span></div>`;
        }
        calendarGrid.innerHTML = html;
    }

    function updateCalendarNav() {
        if (!calPrev || !calNext) return;
        const values = Array.from(monthFilterEl.options).map(o => o.value);
        const idx = values.indexOf(monthFilterEl.value);
        calPrev.disabled = idx < 0 || idx >= values.length - 1;
        calNext.disabled = idx <= 0;
    }

    function getPageList(page, totalPages) {
        if (totalPages <= 5) {
            return Array.from({ length: totalPages }, (_, i) => i + 1);
        }
        const last = totalPages;
        let list = [];
        if (page <= 3) {
            list = [1, 2, 3, 4, last];
        } else if (page >= last - 1) {
            list = [1, last - 3, last - 2, last - 1, last];
        } else {
            list = [1, page - 1, page, page + 1, last];
        }
        return Array.from(new Set(list.filter(n => n >= 1 && n <= last))).sort((a, b) => a - b);
    }

    function buildPagination(page, totalPages) {
        const pages = getPageList(page, totalPages);
        let html = '';
        pages.forEach(function(i) {
            const active = i === page ? 'active' : '';
            html += `<a href="${buildUrl(i)}" data-page="${i}" class="${active}">${i}</a>`;
        });
        paginationEl.innerHTML = html;
    }

    paginationEl.addEventListener('click', function(e) {
        const link = e.target.closest('a[data-page]');
        if (!link) return;
        e.preventDefault();
        const page = parseInt(link.dataset.page, 10);
        if (!page || page < 1) return;

        fetch(`${buildUrl(page)}&ajax=1`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.ok ? res.json() : Promise.reject(res))
        .then(data => {
            const rows = (data.rows || []).map(r => (
                `<tr><td>${r.date || ''}</td><td>${r.check_in || ''}</td><td>${r.check_out || ''}</td></tr>`
            )).join('');
            rowsEl.innerHTML = rows;
            const newPage = data.page || page;
            const newTotal = data.totalPages || 1;
            paginationEl.dataset.page = newPage;
            paginationEl.dataset.total = newTotal;
            if (data.rowsPerPage) {
                paginationEl.dataset.rows = data.rowsPerPage;
                rowsPerPageEl.value = data.rowsPerPage;
            }
            if (data.month) {
                paginationEl.dataset.month = data.month;
                monthFilterEl.value = data.month;
            }
            if (Array.isArray(data.calendar)) {
                calendarData = buildCalendarMap(data.calendar);
            }
            if (totalHoursEl && typeof data.actualHours !== 'undefined') {
                totalHoursEl.textContent = `${data.actualHours} giờ`;
            }
            if (currentSalaryEl && typeof data.currentSalary !== 'undefined') {
                currentSalaryEl.textContent = `${data.currentSalary} VNĐ`;
            }
            renderCalendar(getMonth());
            updateCalendarNav();
            buildPagination(newPage, newTotal);
        })
        .catch(() => {
            window.location.href = link.href;
        });
    });

    rowsPerPageEl.addEventListener('change', function() {
        const page = 1;
        fetch(`${buildUrl(page)}&ajax=1`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.ok ? res.json() : Promise.reject(res))
        .then(data => {
            const rows = (data.rows || []).map(r => (
                `<tr><td>${r.date || ''}</td><td>${r.check_in || ''}</td><td>${r.check_out || ''}</td></tr>`
            )).join('');
            rowsEl.innerHTML = rows;
            const newPage = data.page || page;
            const newTotal = data.totalPages || 1;
            paginationEl.dataset.page = newPage;
            paginationEl.dataset.total = newTotal;
            if (data.rowsPerPage) {
                paginationEl.dataset.rows = data.rowsPerPage;
            }
            if (data.month) {
                paginationEl.dataset.month = data.month;
            }
            if (Array.isArray(data.calendar)) {
                calendarData = buildCalendarMap(data.calendar);
            }
            if (totalHoursEl && typeof data.actualHours !== 'undefined') {
                totalHoursEl.textContent = `${data.actualHours} giờ`;
            }
            if (currentSalaryEl && typeof data.currentSalary !== 'undefined') {
                currentSalaryEl.textContent = `${data.currentSalary} VNĐ`;
            }
            renderCalendar(getMonth());
            updateCalendarNav();
            buildPagination(newPage, newTotal);
        })
        .catch(() => {
            window.location.href = buildUrl(page);
        });
    });

    monthFilterEl.addEventListener('change', function() {
        const page = 1;
        fetch(`${buildUrl(page)}&ajax=1`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.ok ? res.json() : Promise.reject(res))
        .then(data => {
            const rows = (data.rows || []).map(r => (
                `<tr><td>${r.date || ''}</td><td>${r.check_in || ''}</td><td>${r.check_out || ''}</td></tr>`
            )).join('');
            rowsEl.innerHTML = rows;
            const newPage = data.page || page;
            const newTotal = data.totalPages || 1;
            paginationEl.dataset.page = newPage;
            paginationEl.dataset.total = newTotal;
            if (data.rowsPerPage) {
                paginationEl.dataset.rows = data.rowsPerPage;
                rowsPerPageEl.value = data.rowsPerPage;
            }
            if (data.month) {
                paginationEl.dataset.month = data.month;
            }
            if (Array.isArray(data.calendar)) {
                calendarData = buildCalendarMap(data.calendar);
            }
            if (totalHoursEl && typeof data.actualHours !== 'undefined') {
                totalHoursEl.textContent = `${data.actualHours} giờ`;
            }
            if (currentSalaryEl && typeof data.currentSalary !== 'undefined') {
                currentSalaryEl.textContent = `${data.currentSalary} VNĐ`;
            }
            renderCalendar(getMonth());
            updateCalendarNav();
            buildPagination(newPage, newTotal);
        })
        .catch(() => {
            window.location.href = buildUrl(page);
        });
    });

    const initialPage = parseInt(paginationEl.dataset.page || '1', 10) || 1;
    const initialTotal = parseInt(paginationEl.dataset.total || '1', 10) || 1;
    buildPagination(initialPage, initialTotal);
    renderCalendar(getMonth());
    updateCalendarNav();

    if (calendarToggle && calendarPopover) {
        calendarToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            calendarPopover.classList.toggle('is-open');
        });
        document.addEventListener('click', function(e) {
            if (!calendarPopover.contains(e.target) && !calendarToggle.contains(e.target)) {
                calendarPopover.classList.remove('is-open');
            }
        });
    }

    if (calPrev) {
        calPrev.addEventListener('click', function() {
            const values = Array.from(monthFilterEl.options).map(o => o.value);
            const idx = values.indexOf(monthFilterEl.value);
            if (idx >= 0 && idx < values.length - 1) {
                monthFilterEl.value = values[idx + 1];
                monthFilterEl.dispatchEvent(new Event('change'));
            }
        });
    }
    if (calNext) {
        calNext.addEventListener('click', function() {
            const values = Array.from(monthFilterEl.options).map(o => o.value);
            const idx = values.indexOf(monthFilterEl.value);
            if (idx > 0) {
                monthFilterEl.value = values[idx - 1];
                monthFilterEl.dispatchEvent(new Event('change'));
            }
        });
    }
});
</script>
</body>
</html>
