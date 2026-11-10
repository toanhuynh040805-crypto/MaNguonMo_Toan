@extends('layouts.app')
@section('content')
<div class="container pb-5">
    <div class="row g-4"><aside class="col-lg-3 col-xl-2"><div class="admin-sidebar p-3">
        <div class="small text-muted fw-bold px-3 mb-3">QUẢN TRỊ VIÊN</div>
        <a class="admin-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">📊 Tổng quan</a>
        <a class="admin-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">📁 Danh mục</a>
        <a class="admin-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">📦 Sản phẩm</a>
        <a class="admin-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">🧾 Đơn hàng</a>
        <hr><a class="admin-link" href="{{ route('home') }}">↗ Xem cửa hàng</a>
    </div></aside>
    <div class="col-lg-9 col-xl-10">@yield('admin-content')</div></div>
</div>
@endsection
