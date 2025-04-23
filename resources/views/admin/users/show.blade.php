@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">User #{{ $user->id }}: {{ $user->name }}</h1>

    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Đăng ký:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>

    <form action="{{ route('admin.users.resetPassword', $user) }}"
          method="POST" class="d-inline-block me-2">
      @csrf
      <button class="btn btn-warning">Reset mật khẩu về 123456789</button>
    </form>
    <form action="{{ route('admin.users.destroy', $user) }}"
          method="POST" class="d-inline-block"
          onsubmit="return confirm('Xóa user này?');">
      @csrf
      @method('DELETE')
      <button class="btn btn-danger">Xóa user</button>
    </form>

    <hr>

    <h3>Đơn hàng của user</h3>
    @if($orders->isEmpty())
      <p>Chưa có đơn hàng nào.</p>
    @else
      <table class="table table-bordered">
        <thead class="table-light">
          <tr>
            <th>#Order</th><th>Ngày</th><th>Tổng</th><th>Sản phẩm</th>
          </tr>
        </thead>
        <tbody>
          @foreach($orders as $o)
          <tr>
            <td>{{ $o->id }}</td>
            <td>{{ $o->created_at->format('d/m/Y H:i') }}</td>
            <td>{{ number_format($o->total,0,',','.') }}₫</td>
            <td>
              <ul class="mb-0">
                @foreach($o->items as $it)
                  <li>
                    {{ $it->product->name }} × {{ $it->quantity }}
                    @if($it->options)
                      <ul>
                        @php
                          $vals = \App\Models\OptionValue::whereIn('id',$it->options)
                                     ->with('type')->get();
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
    @endif
</div>
@endsection
