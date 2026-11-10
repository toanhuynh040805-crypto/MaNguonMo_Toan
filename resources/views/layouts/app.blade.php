<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'OpenShop') | OpenShop</title>
    <meta name="description" content="OpenShop - Cửa hàng phụ kiện công nghệ với trải nghiệm mua sắm đơn giản.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/openshop.css') }}" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light sticky-top top-nav">
    <div class="container">
        <a class="navbar-brand brand" href="{{ route('home') }}"><span class="brand-mark">O</span>OpenShop<span class="brand-dot">.</span></a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Mở menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto ms-lg-4 align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Cửa hàng</a></li>
                @auth
                    @if(auth()->user()->isAdmin())
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Bảng điều khiển</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.orders.index') }}">Đơn hàng</a></li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('orders.index') }}">Đơn hàng của tôi</a></li>
                    @endif
                @endauth
            </ul>
            <div class="d-flex align-items-center flex-wrap gap-2 mt-3 mt-lg-0">
                @guest
                    <a class="btn btn-link text-dark text-decoration-none fw-semibold" href="{{ route('login') }}">Đăng nhập</a>
                    <a class="btn btn-primary rounded-pill px-4" href="{{ route('register') }}">Đăng ký</a>
                @else
                    @if(!auth()->user()->isAdmin())
                        <a class="btn btn-light border rounded-pill px-3" href="{{ route('cart.index') }}">🛒 Giỏ hàng <span class="badge bg-dark ms-1" data-cart-count>{{ array_sum(session('cart', [])) }}</span></a>
                    @endif
                    <a class="btn btn-light border rounded-pill px-3" href="{{ route('profile.edit') }}">👤 Tài khoản</a>
                    <span class="small text-muted d-none d-xl-block">Xin chào, <strong>{{ auth()->user()->name }}</strong></span>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">@csrf<button class="btn btn-outline-secondary rounded-pill px-3">Đăng xuất</button></form>
                @endguest
            </div>
        </div>
    </div>
</nav>

<main class="site-main">
    <div class="container pt-4">
        @if(session('success'))<div role="alert" class="alert alert-success rounded-4">{{ session('success') }}</div>@endif
        @if(session('error'))<div role="alert" class="alert alert-danger rounded-4">{{ session('error') }}</div>@endif
        @if($errors->any())
            <div role="alert" class="alert alert-danger rounded-4"><strong>Vui lòng kiểm tra:</strong>
                <ul class="mb-0 mt-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
    </div>
    @yield('content')
</main>

<footer class="site-footer mt-5">
    <div class="container py-5">
        <div class="d-flex justify-content-between flex-wrap gap-3 align-items-start">
            <div><div class="brand mb-2"><span class="brand-mark">O</span>OpenShop<span class="brand-dot">.</span></div><p class="text-muted mb-0 small">Đồ án Lập trình mã nguồn mở · Laravel + MySQL</p></div>
            <div class="small text-muted">Website demo học tập · Phương thức thanh toán: COD<br>© {{ date('Y') }} OpenShop</div>
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/shop.js') }}" defer></script>
@stack('scripts')
</body>
</html>
