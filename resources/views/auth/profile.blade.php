@extends('layouts.app')
@section('title', 'Tài khoản của tôi')
@section('content')
<div class="container pb-5">
    <div class="mb-4"><span class="eyebrow text-primary">THÔNG TIN CÁ NHÂN</span><h1 class="section-title">Tài khoản của tôi</h1><p class="text-muted mb-0">Cập nhật tên, email hoặc thay đổi mật khẩu đăng nhập.</p></div>
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="panel p-4 p-lg-5 h-100">
                <h2 class="h5 fw-bold mb-4">Thông tin tài khoản</h2>
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="mb-3"><label class="form-label fw-semibold" for="profile_name">Họ và tên</label><input class="form-control form-control-lg" type="text" id="profile_name" name="name" value="{{ old('name', $user->name) }}" maxlength="120" required autocomplete="name"></div>
                    <div class="mb-3"><label class="form-label fw-semibold" for="profile_email">Email</label><input class="form-control form-control-lg" type="email" id="profile_email" name="email" value="{{ old('email', $user->email) }}" maxlength="255" required autocomplete="email"></div>
                    <div class="mb-4 small text-muted">Vai trò: <strong>{{ $user->isAdmin() ? 'Quản trị viên' : 'Khách hàng' }}</strong> (không thể tự thay đổi).</div>
                    <button type="submit" class="btn btn-primary px-4">Lưu thông tin</button>
                </form>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="panel p-4 p-lg-5 h-100">
                <h2 class="h5 fw-bold mb-4">Đổi mật khẩu</h2>
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="mb-3"><label class="form-label fw-semibold" for="current_password">Mật khẩu hiện tại</label><input class="form-control form-control-lg" type="password" id="current_password" name="current_password" autocomplete="current-password" required></div>
                    <div class="mb-3"><label class="form-label fw-semibold" for="new_password">Mật khẩu mới (tối thiểu 8 ký tự)</label><input class="form-control form-control-lg" type="password" id="new_password" name="password" minlength="8" autocomplete="new-password" required></div>
                    <div class="mb-4"><label class="form-label fw-semibold" for="password_confirmation">Nhập lại mật khẩu mới</label><input class="form-control form-control-lg" type="password" id="password_confirmation" name="password_confirmation" minlength="8" autocomplete="new-password" required></div>
                    <button type="submit" class="btn btn-outline-primary px-4">Đổi mật khẩu</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
