@extends('layouts.admin')

@section('content')
<style>
  /* mọi th nền #4ab3af, viền trắng 2px, chữ trắng, căn giữa */
  .table.table-bordered.align-middle th {
    background-color: #4ab3af !important;
    border: 2px solid #fff      !important;
    color: #fff                 !important;
    text-align: center;
    vertical-align: middle;
  }
  /* mọi td căn giữa */
  .table.table-bordered.align-middle td {
    text-align: center;
    vertical-align: middle;
  }
  /* với những td có colspan thì canh trái, bỏ padding */
  .table.table-bordered.align-middle td[colspan] {
    text-align: left;
    padding-left: 0 !important;
  }
</style>

<div class="container py-4">
  <h2 class="mb-4" style="color: #b83232; font-size: 3rem;">Danh sách Đơn hàng</h2>

  <table class="table table-bordered align-middle">
    <thead class="table-light">
      <tr>
        <th>#</th><th>Khách</th><th>Điện thoại</th><th>Tổng</th>
        <th>Thanh toán</th><th>Trạng thái</th><th>Ngày</th>
      </tr>
    </thead>
    <tbody>
      @foreach($orders as $o)
        <tr>
          <td>{{ $o->id }}</td>
          <td>{{ $o->fullname }}<br><small>{{ $o->address }}</small></td>
          <td>{{ $o->phone }}</td>
          <td>{{ number_format($o->total,0,',','.') }}₫</td>
          <td>
            @if($o->payment_method=='cod')
              COD
            @else
              CK<br><small>{{ $o->bank_ref }}</small>
            @endif
          </td>
          <td>{{ ucfirst($o->status) }}</td>
          <td>{{ $o->created_at->format('d/m H:i') }}</td>
        </tr>
        <tr>
          <td colspan="7">
            <ul class="mb-0">
              @foreach($o->items as $it)
                <li>
                  {{ $it->product->name }} × {{ $it->quantity }}
                  @if($it->options)
                    <ul>
                      @php
                        $vals = \App\Models\OptionValue::whereIn('id', $it->options)->with('type')->get();
                      @endphp
                      @foreach($vals as $v)
                        <li>{{ $v->type->name }}: {{ $v->value }}</li>
                      @endforeach
                    </ul>
                  @endif
                </li>
              @endforeach
            </ul>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>

  {{ $orders->links() }}
</div>
@endsection
