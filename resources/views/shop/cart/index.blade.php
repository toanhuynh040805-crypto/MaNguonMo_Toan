@extends('layouts.app')
@section('title', 'Giỏ hàng')
@section('content')
<div class="container pb-5">
    <div class="mb-4"><span class="eyebrow text-primary">SẮP HOÀN TẤT</span><h1 class="section-title">Giỏ hàng của bạn 🛒</h1></div>
    @if($items->isEmpty())
        <div class="empty-state"><div class="display-4 mb-3">🛍️</div><h2 class="h4">Giỏ hàng trống</h2><p class="text-muted">Chọn sản phẩm yêu thích để bắt đầu.</p><a href="{{ route('home') }}" class="btn btn-primary rounded-pill px-4">Khám phá sản phẩm</a></div>
    @else
        <div class="row g-4"><div class="col-lg-8"><div class="panel p-3 p-md-4">
            @foreach($items as $item)
                <div class="d-flex gap-3 py-3 {{ !$loop->last ? 'border-bottom' : '' }} align-items-center flex-wrap">
                    <x-product-art :product="$item['product']" class="cart-thumbnail" />
                    <div class="flex-grow-1"><a href="{{ route('products.show', $item['product']->slug) }}" class="fw-bold text-decoration-none text-dark">{{ $item['product']->name }}</a><div class="small text-muted">{{ number_format($item['product']->price, 0, ',', '.') }}₫/sp</div>
                        @if(!$item['product']->is_active || $item['product']->stock < $item['quantity'])<span class="text-danger small">Không đủ hàng (còn {{ $item['product']->stock }})</span>@endif
                    </div>
                    <form method="POST" action="{{ route('cart.update', $item['product']) }}" class="d-flex gap-2 align-items-center">@csrf @method('PATCH')<label class="visually-hidden" for="quantity-{{ $item['product']->id }}">Số lượng</label><input id="quantity-{{ $item['product']->id }}" class="form-control" type="number" name="quantity" min="1" max="99" value="{{ $item['quantity'] }}" style="width:82px"><button class="btn btn-outline-secondary btn-sm">Cập nhật</button></form>
                    <form method="POST" action="{{ route('cart.destroy', $item['product']) }}">@csrf @method('DELETE')<button class="btn btn-link text-danger btn-sm" aria-label="Xóa {{ $item['product']->name }}">Xóa</button></form>
                </div>
            @endforeach
        </div></div>
        <div class="col-lg-4"><div class="panel p-4 sticky-summary"><h2 class="h5 fw-bold">Thông tin đơn hàng</h2><hr><div class="d-flex justify-content-between mb-3"><span>Tạm tính</span><strong>{{ number_format($total, 0, ',', '.') }}₫</strong></div><div class="d-flex justify-content-between mb-4"><span>Phí vận chuyển</span><span class="text-muted">Chưa áp dụng</span></div><hr><div class="d-flex justify-content-between h5 fw-bold mb-4"><span>Tổng cộng</span><span class="text-primary">{{ number_format($total, 0, ',', '.') }}₫</span></div>
            @if($canCheckout)<a href="{{ route('checkout.create') }}" class="btn btn-primary btn-lg w-100">Tiến hành đặt hàng →</a>@else<button class="btn btn-secondary w-100" disabled>Kiểm tra lại giỏ hàng</button>@endif
            <a href="{{ route('home') }}" class="btn btn-link w-100 text-decoration-none mt-2">← Tiếp tục mua sắm</a></div></div></div>
    @endif
</div>
@endsection
