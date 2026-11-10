@extends('layouts.admin')
@section('title', 'Quản lý đơn hàng')
@section('admin-content')
<div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-4"><h1 class="section-title mb-0">Quản lý đơn hàng</h1><a class="btn btn-outline-primary" href="{{ route('admin.orders.export', request()->only(['q', 'status', 'date_from', 'date_to'])) }}">⬇ Xuất CSV (theo bộ lọc)</a></div>
<form method="GET" action="{{ route('admin.orders.index') }}" class="panel p-3 mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-6 col-xl-4"><label for="order_q" class="form-label small">Mã đơn, người nhận hoặc email</label><input id="order_q" name="q" class="form-control" maxlength="100" value="{{ request('q') }}" placeholder="DH000001, email..."></div>
        <div class="col-md-6 col-xl-3"><label for="order_status" class="form-label small">Trạng thái</label><select id="order_status" name="status" class="form-select"><option value="">Tất cả trạng thái</option>@foreach(\App\Models\Order::STATUSES as $status => $label)<option value="{{ $status }}" @selected(request('status') === $status)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-6 col-xl-2"><label for="date_from" class="form-label small">Từ ngày</label><input id="date_from" name="date_from" type="date" class="form-control" value="{{ request('date_from') }}"></div>
        <div class="col-md-6 col-xl-2"><label for="date_to" class="form-label small">Đến ngày</label><input id="date_to" name="date_to" type="date" class="form-control" value="{{ request('date_to') }}"></div>
        <div class="col-xl-1 d-grid"><button class="btn btn-primary">Lọc</button></div>
    </div>
    <div class="text-end mt-2"><a href="{{ route('admin.orders.index') }}" class="small text-decoration-none">Xóa bộ lọc</a></div>
</form>
<div class="panel overflow-hidden"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Mã đơn</th><th>Khách hàng</th><th>Ngày đặt</th><th>Tổng tiền</th><th>Trạng thái</th><th></th></tr></thead><tbody>
@forelse($orders as $order)
<tr><td class="fw-bold">{{ $order->code }}</td><td>{{ $order->recipient_name }}<br><span class="small text-muted">{{ $order->user->email }}</span></td><td class="text-nowrap">{{ $order->created_at->format('d/m/Y H:i') }}</td><td class="text-nowrap">{{ number_format($order->total_amount, 0, ',', '.') }}₫</td><td><x-order-badge :status="$order->status" /></td><td><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">Chi tiết</a></td></tr>
@empty<tr><td colspan="6" class="py-5 text-center text-muted">Không tìm thấy đơn hàng.</td></tr>@endforelse
</tbody></table></div></div><div class="mt-4">{{ $orders->links() }}</div>
@endsection
