@extends('layouts.app')
@section('title', 'Đơn hàng '.$order->code)
@section('content')
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-4"><div><a href="{{ route('orders.index') }}" class="text-muted small text-decoration-none">← Đơn hàng của tôi</a><h1 class="section-title mt-2">Đơn hàng {{ $order->code }}</h1><div class="small text-muted">Đặt lúc {{ $order->created_at->format('d/m/Y H:i') }}</div></div><x-order-badge :status="$order->status" /></div>
    <div class="d-flex justify-content-end mb-3"><a class="btn btn-outline-primary" href="{{ route('orders.receipt', $order) }}" target="_blank" rel="noopener">🖨 In phiếu đơn hàng</a></div>
    <div class="row g-4"><div class="col-lg-8"><div class="panel p-4"><h2 class="h5 fw-bold mb-3">Danh sách sản phẩm</h2>
        @foreach($order->items as $item)<div class="d-flex justify-content-between gap-3 py-3 border-bottom"><div><strong>{{ $item->product_name }}</strong><div class="text-muted small">{{ number_format($item->price, 0, ',', '.') }}₫ × {{ $item->quantity }}</div></div><strong class="text-nowrap">{{ number_format($item->subtotal, 0, ',', '.') }}₫</strong></div>@endforeach
        <div class="d-flex justify-content-between h5 fw-bold mt-4"><span>Tổng cộng</span><span class="text-primary">{{ number_format($order->total_amount, 0, ',', '.') }}₫</span></div>
    </div></div><div class="col-lg-4"><div class="panel p-4"><h2 class="h5 fw-bold mb-3">Người nhận</h2><dl><dt>Họ tên</dt><dd>{{ $order->recipient_name }}</dd><dt>Số điện thoại</dt><dd>{{ $order->phone }}</dd><dt>Địa chỉ</dt><dd>{{ $order->address }}</dd><dt>Ghi chú</dt><dd>{{ $order->note ?: 'Không có' }}</dd><dt>Thanh toán</dt><dd>COD (khi nhận hàng)</dd></dl>
        @if($order->status === 'pending')
            <form method="POST" action="{{ route('orders.cancel', $order) }}" onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn đang chờ xác nhận?')">@csrf @method('PATCH')<button class="btn btn-outline-danger w-100">Hủy đơn hàng</button></form>
        @endif
    </div></div></div>
</div>
<div class="container pb-4"><div class="panel p-4">
    <h2 class="h5 fw-bold mb-3">Lịch sử xử lý đơn hàng</h2>
    <x-order-timeline :histories="$order->statusHistories" />
</div></div>
@endsection
