@if(count($groupedAtt) === 0)
  <tr>
    <td colspan="5" style="color:red; font-weight:bold;">
      Kh&#244;ng c&#243; d&#7919; li&#7879;u ch&#7845;m c&#244;ng
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
      <td>{{ $dailyHours }} gi&#7901;</td>
    </tr>
  @endforeach
@endif
