@extends('layouts.app')
@section('title', 'Đăng nhập')
@section('content')
<div class="container py-4 py-md-5"><div class="auth-card mx-auto">
    <div class="text-center mb-4"><span class="auth-icon">🔐</span><h1 class="fw-bold h2 mt-3">Chào mừng trở lại!</h1><p class="text-muted">Đăng nhập để tiếp tục mua sắm.</p></div>
    <form method="POST" action="{{ route('login.store') }}">@csrf
        <div class="mb-3"><label class="form-label fw-semibold" for="email">Email</label><input id="email" name="email" type="email" class="form-control form-control-lg" value="{{ old('email') }}" autocomplete="email" required autofocus></div>
        <div class="mb-3"><label class="form-label fw-semibold" for="password">Mật khẩu</label><input id="password" name="password" type="password" class="form-control form-control-lg" autocomplete="current-password" required></div>
        <div class="form-check mb-4"><input class="form-check-input" type="checkbox" value="1" name="remember" id="remember"><label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label></div>
        <button class="btn btn-primary btn-lg w-100">Đăng nhập</button>
    </form>
    <p class="text-center mt-4 mb-0 small">Chưa có tài khoản? <a href="{{ route('register') }}" class="fw-semibold">Đăng ký miễn phí</a></p>
</div></div>
@endsection
