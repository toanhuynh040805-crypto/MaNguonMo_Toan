@extends('layouts.app')
@section('title', 'Đặt hàng')
@section('content')
<div class="container pb-5"><div class="mb-4"><span class="eyebrow text-primary">BƯỚC CUỐI CÙNG</span><h1 class="section-title">Thông tin đặt hàng</h1></div>
<form method="POST" action="{{ route('checkout.store') }}" class="row g-4">@csrf
    <div class="col-lg-7"><div class="panel p-4 p-lg-5"><h2 class="h5 fw-bold mb-4">Thông tin người nhận</h2>
        <div class="mb-3"><label class="form-label" for="recipient_name">Họ tên người nhận *</label><input id="recipient_name" class="form-control form-control-lg" name="recipient_name" maxlength="120" value="{{ old('recipient_name', auth()->user()->name) }}" required></div>
        <div class="mb-3"><label class="form-label" for="phone">Số điện thoại *</label><input id="phone" class="form-control form-control-lg" name="phone" inputmode="numeric" maxlength="11" value="{{ old('phone') }}" placeholder="09xxxxxxxx" required></div>
        <div class="mb-3"><label class="form-label" for="address">Địa chỉ giao hàng *</label><textarea id="address" class="form-control" name="address" rows="3" maxlength="500" required>{{ old('address') }}</textarea></div>
        <div class="mb-3"><label class="form-label" for="note">Ghi chú (không bắt buộc)</label><textarea id="note" class="form-control" name="note" rows="3" maxlength="1000">{{ old('note') }}</textarea></div>
        <div class="rounded-4 bg-light p-3"><strong>💵 Thanh toán khi nhận hàng (COD)</strong><div class="small text-muted mt-1">Đây là website demo: không thu tiền trực tuyến.</div></div>
    </div></div>
    <div class="col-lg-5"><div class="panel p-4 sticky-summary"><h2 class="h5 fw-bold mb-3">Đơn hàng của bạn</h2>
        @foreach($items as $item)
            <div class="d-flex justify-content-between gap-2 border-bottom py-3"><span>{{ $item['product']->name }} <span class="text-muted">× {{ $item['quantity'] }}</span></span><strong class="text-nowrap">{{ number_format($item['subtotal'], 0, ',', '.') }}₫</strong></div>
        @endforeach
        <div class="d-flex justify-content-between fw-bold fs-5 my-4"><span>Tổng thanh toán</span><span class="text-primary">{{ number_format($total, 0, ',', '.') }}₫</span></div>
        <button class="btn btn-primary btn-lg w-100">Xác nhận đặt hàng →</button><p class="text-muted small text-center mt-3 mb-0">Hệ thống kiểm tra lại tồn kho trước khi tạo đơn.</p>
    </div></div>
</form></div>
@endsection
