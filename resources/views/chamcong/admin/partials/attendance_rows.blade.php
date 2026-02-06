@if(count($groupedAtt) === 0)
    <tr>
        <td colspan="6" style="color:red; font-weight:bold;">
            Kh&ocirc;ng c&oacute; d&#7919; li&#7879;u ch&#7845;m c&ocirc;ng th&aacute;ng n&agrave;y
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
                <td>{{ $dailyHours }} gi&#7901;</td>
                <td class="action-buttons">
                    <button type="submit">L&#432;u</button>
            </form>
                    <form method="POST" action="{{ route('chamcong.admin.attendance.delete-day') }}" style="display:inline;"
                        onsubmit="return confirm('B&#7841;n c&oacute; ch&#7855;c ch&#7855;n mu&#7889;n x&oacute;a to&agrave;n b&#7897; ch&#7845;m c&ocirc;ng ng&agrave;y {{ $g->work_date }} c&#7911;a user {{ $g->username }}?');">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $g->user_id }}">
                        <input type="hidden" name="the_date" value="{{ $g->work_date }}">
                        <button type="submit" style="color:white; background-color:#b83232;">X&oacute;a</button>
                    </form>
                </td>
        </tr>
    @endforeach
@endif


