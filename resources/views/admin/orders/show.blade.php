@extends('layouts.admin')
@section('title', 'Chi tiết '.$order->code)
@section('admin-content')
<a href="{{ route('admin.orders.index') }}" class="small text-decoration-none">← Quản lý đơn hàng</a>
<div class="d-flex justify-content-between flex-wrap gap-2 align-items-center mt-2 mb-4"><div><h1 class="section-title mb-0">Đơn hàng {{ $order->code }}</h1><div class="small text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</div></div><div class="d-flex gap-2 align-items-center"><a class="btn btn-outline-primary btn-sm" href="{{ route('admin.orders.receipt', $order) }}" target="_blank" rel="noopener">🖨 In phiếu</a><x-order-badge :status="$order->status" /></div></div>
<div class="row g-4"><div class="col-xl-7"><div class="panel p-4"><h2 class="h5 fw-bold">Sản phẩm trong đơn</h2>@foreach($order->items as $item)
<div class="d-flex justify-content-between gap-3 border-bottom py-3"><div><strong>{{ $item->product_name }}</strong><div class="small text-muted">{{ number_format($item->price, 0, ',', '.') }}₫ × {{ $item->quantity }}</div></div><strong>{{ number_format($item->subtotal, 0, ',', '.') }}₫</strong></div>
@endforeach<div class="d-flex justify-content-between mt-4 fw-bold h5"><span>Tổng tiền</span><span class="text-primary">{{ number_format($order->total_amount, 0, ',', '.') }}₫</span></div></div></div>
<div class="col-xl-5"><div class="panel p-4 mb-4"><h2 class="h5 fw-bold">Giao hàng</h2><p class="mb-1"><strong>{{ $order->recipient_name }}</strong> — {{ $order->phone }}</p><p>{{ $order->address }}</p><p class="text-muted small mb-0">Ghi chú: {{ $order->note ?: 'Không có' }}</p></div>
<div class="panel p-4"><h2 class="h5 fw-bold mb-3">Cập nhật trạng thái</h2>
@if(count(\App\Models\Order::TRANSITIONS[$order->status] ?? []) > 0)
    <form method="POST" action="{{ route('admin.orders.status', $order) }}">@csrf @method('PATCH')<label class="form-label" for="status">Chuyển trạng thái</label><select id="status" name="status" class="form-select mb-3">@foreach(\App\Models\Order::TRANSITIONS[$order->status] as $status)<option value="{{ $status }}">{{ \App\Models\Order::STATUSES[$status] }}</option>@endforeach</select><button class="btn btn-primary w-100">Xác nhận cập nhật</button></form>
@else<p class="text-muted mb-0">Đơn hàng đã kết thúc, không thể đổi trạng thái.</p>@endif
<p class="small text-muted mt-3 mb-0">Khi đơn bị hủy, hệ thống trả lại số lượng đã trừ từ tồn kho.</p></div></div></div>
<div class="panel p-4 mt-4">
    <h2 class="h5 fw-bold mb-3">Lịch sử xử lý đơn hàng</h2>
    <x-order-timeline :histories="$order->statusHistories" :show-actor="true" />
</div>
@endsection
