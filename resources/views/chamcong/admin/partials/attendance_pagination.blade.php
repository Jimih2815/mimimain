@php
    $baseParams = [
        'tab' => 'modul4',
        'filter_user_id' => $filterUID,
        'rows_per_page' => $rowsPerPage,
        'start_date' => $startDate,
        'end_date' => $endDate,
    ];
@endphp
@php
    $startPage = $page - 1;
    $endPage = $page + 1;
    if ($page <= 2) { $startPage = 1; $endPage = 3; }
    if ($page >= $totalPages - 1) { $startPage = $totalPages - 2; $endPage = $totalPages; }
    if ($startPage < 1) $startPage = 1;
    if ($endPage > $totalPages) $endPage = $totalPages;
@endphp
@for($i = $startPage; $i <= $endPage; $i++)
    <a href="{{ route('chamcong.admin.dashboard', $baseParams + ['page' => $i]) }}#table-chamcong"
       data-page="{{ $i }}"
       class="{{ $i == $page ? 'active' : '' }}">{{ $i }}</a>
@endfor
