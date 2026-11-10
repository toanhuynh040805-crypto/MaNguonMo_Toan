@extends('layouts.admin')
@section('title', 'Tổng quan quản trị')
@section('admin-content')
<div class="d-flex justify-content-between flex-wrap gap-3 align-items-center mb-4"><div><span class="eyebrow text-primary">TỔNG QUAN CỬA HÀNG</span><h1 class="section-title mb-0">Bảng điều khiển</h1></div><a class="btn btn-primary rounded-pill" href="{{ route('admin.products.create') }}">+ Thêm sản phẩm</a></div>
<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3"><div class="stat-box"><span>📁 Danh mục</span><strong>{{ $categoryCount }}</strong><small>Nhóm sản phẩm</small></div></div>
    <div class="col-md-6 col-xl-3"><div class="stat-box"><span>📦 Sản phẩm</span><strong>{{ $productCount }}</strong><small>Trong hệ thống</small></div></div>
    <div class="col-md-6 col-xl-3"><div class="stat-box"><span>🧾 Đơn hàng</span><strong>{{ $orderCount }}</strong><small>{{ $pendingCount }} đơn chờ xử lý</small></div></div>
    <div class="col-md-6 col-xl-3"><div class="stat-box"><span>💰 Doanh thu hoàn tất</span><strong class="fs-4">{{ number_format($revenue, 0, ',', '.') }}₫</strong><small>Chỉ tính đơn hoàn tất</small></div></div>
</div>
<div class="panel overflow-hidden"><div class="p-4 d-flex justify-content-between align-items-center"><h2 class="h5 fw-bold mb-0">Đơn hàng gần đây</h2><a href="{{ route('admin.orders.index') }}" class="small">Xem tất cả →</a></div>
<div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Mã đơn</th><th>Khách hàng</th><th>Giá trị</th><th>Trạng thái</th><th></th></tr></thead><tbody>@forelse($recentOrders as $order)
    <tr><td class="fw-semibold">{{ $order->code }}</td><td>{{ $order->user->name }}</td><td>{{ number_format($order->total_amount, 0, ',', '.') }}₫</td><td><x-order-badge :status="$order->status" /></td><td><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.orders.show', $order) }}">Xem</a></td></tr>
@empty<tr><td colspan="5" class="py-4 text-center text-muted">Chưa có đơn hàng.</td></tr>@endforelse</tbody></table></div></div>
@endsection
