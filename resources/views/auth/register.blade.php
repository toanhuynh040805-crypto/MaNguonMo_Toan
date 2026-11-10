@extends('layouts.app')
@section('title', 'Tạo tài khoản')
@section('content')
<div class="container py-4 py-md-5"><div class="auth-card mx-auto">
    <div class="text-center mb-4"><span class="auth-icon">👤</span><h1 class="fw-bold h2 mt-3">Tạo tài khoản</h1><p class="text-muted">Bắt đầu trải nghiệm mua sắm ngay hôm nay.</p></div>
    <form method="POST" action="{{ route('register.store') }}">@csrf
        <div class="mb-3"><label class="form-label fw-semibold" for="name">Họ và tên</label><input id="name" name="name" class="form-control form-control-lg" value="{{ old('name') }}" maxlength="120" required></div>
        <div class="mb-3"><label class="form-label fw-semibold" for="email">Email</label><input id="email" name="email" type="email" class="form-control form-control-lg" value="{{ old('email') }}" required></div>
        <div class="mb-3"><label class="form-label fw-semibold" for="password">Mật khẩu (tối thiểu 8 ký tự)</label><input id="password" name="password" type="password" class="form-control form-control-lg" minlength="8" autocomplete="new-password" required></div>
        <div class="mb-4"><label class="form-label fw-semibold" for="password_confirmation">Nhập lại mật khẩu</label><input id="password_confirmation" name="password_confirmation" type="password" class="form-control form-control-lg" minlength="8" autocomplete="new-password" required></div>
        <button class="btn btn-primary btn-lg w-100">Đăng ký</button>
    </form>
    <p class="text-center mt-4 mb-0 small">Đã có tài khoản? <a href="{{ route('login') }}" class="fw-semibold">Đăng nhập</a></p>
</div></div>
@endsection
