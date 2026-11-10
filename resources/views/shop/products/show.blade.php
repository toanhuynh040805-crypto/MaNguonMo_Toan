@extends('layouts.app')
@section('title', $product->name)
@section('content')
<div class="container pb-5">
    <div class="small mb-4"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Cửa hàng</a> <span class="text-muted mx-2">/</span> {{ $product->category->name }} <span class="text-muted mx-2">/</span> {{ $product->name }}</div>
    <div class="row g-4 g-lg-5 align-items-start">
        <div class="col-lg-6"><x-product-art :product="$product" class="product-art-detail" /></div>
        <div class="col-lg-6 pt-lg-3">
            <span class="eyebrow text-primary">{{ $product->category->name }}</span>
            <h1 class="fw-bold display-6 mt-3 mb-3">{{ $product->name }}</h1>
            <div class="detail-price mb-3">{{ number_format($product->price, 0, ',', '.') }}₫</div>
            <p class="text-muted fs-6 lh-lg">{{ $product->description ?: 'Sản phẩm công nghệ chất lượng, dễ dàng sử dụng.' }}</p>
            <p class="fw-medium {{ $product->stock > 0 ? 'text-success' : 'text-danger' }}">{{ $product->stock > 0 ? '● Còn '.$product->stock.' sản phẩm' : '● Đã hết hàng' }}</p>
            @auth
                @if(!auth()->user()->isAdmin() && $product->stock > 0)
                    <form method="POST" action="{{ route('cart.add', $product) }}" class="d-flex flex-wrap gap-3 mt-4" data-ajax-cart>@csrf
                        <label for="quantity" class="visually-hidden">Số lượng</label><input id="quantity" type="number" name="quantity" min="1" max="{{ min($product->stock, 99) }}" value="1" class="form-control form-control-lg" style="max-width:100px" required>
                        <button class="btn btn-primary btn-lg rounded-pill px-5">🛒 Thêm vào giỏ hàng</button>
                    </form>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg rounded-pill px-4 mt-3">Đăng nhập để đặt mua</a>
            @endauth
            <div class="d-flex gap-4 flex-wrap text-muted small mt-4"><span>✓ Thanh toán COD</span><span>✓ Quản lý đơn hàng trực tuyến</span></div>
        </div>
    </div>
    @if($related->isNotEmpty())
        <h2 class="h3 fw-bold mt-5 mb-4">Có thể bạn quan tâm</h2><div class="row g-3">@foreach($related as $item)<div class="col-6 col-lg-3"><x-product-card :product="$item" /></div>@endforeach</div>
    @endif
</div>
@endsection
