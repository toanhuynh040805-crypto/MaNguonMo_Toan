@extends('layouts.app')
@section('title', 'Đơn hàng của tôi')
@section('content')
<div class="container pb-5"><div class="mb-4"><span class="eyebrow text-primary">TÀI KHOẢN</span><h1 class="section-title">Đơn hàng của tôi</h1></div>
    <form method="GET" action="{{ route('orders.index') }}" class="panel p-3 mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-5"><label for="my_order_q" class="form-label small">Mã đơn hàng</label><input id="my_order_q" name="q" class="form-control" maxlength="30" placeholder="Nhập mã đơn hàng" value="{{ request('q') }}"></div>
            <div class="col-md-4"><label for="my_order_status" class="form-label small">Trạng thái</label><select id="my_order_status" name="status" class="form-select"><option value="">Tất cả trạng thái</option>@foreach(\App\Models\Order::STATUSES as $status => $label)<option value="{{ $status }}" @selected(request('status') === $status)>{{ $label }}</option>@endforeach</select></div>
            <div class="col-md-2 d-grid"><button class="btn btn-primary">Tìm đơn</button></div>
            <div class="col-md-1 text-md-end"><a class="small text-decoration-none" href="{{ route('orders.index') }}">Xóa lọc</a></div>
        </div>
    </form>
    <div class="panel overflow-hidden">
        <div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>Mã đơn</th><th>Ngày đặt</th><th>Tổng tiền</th><th>Trạng thái</th><th></th></tr></thead><tbody>
            @forelse($orders as $order)<tr><td class="fw-bold">{{ $order->code }}</td><td>{{ $order->created_at->format('d/m/Y H:i') }}</td><td>{{ number_format($order->total_amount, 0, ',', '.') }}₫</td><td><x-order-badge :status="$order->status" /></td><td><a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">Chi tiết</a></td></tr>
            @empty<tr><td colspan="5" class="text-center py-5 text-muted">Bạn chưa có đơn hàng nào. <a href="{{ route('home') }}">Mua sắm ngay</a></td></tr>@endforelse
        </tbody></table></div>
    </div><div class="mt-4">{{ $orders->links() }}</div>
</div>
@endsection
