@props(['product'])
<div class="card product-card h-100 border-0">
    <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none">
        <x-product-art :product="$product" />
    </a>
    <div class="card-body d-flex flex-column p-3 p-md-4">
        <div class="small text-muted mb-1">{{ $product->category?->name }}</div>
        <a href="{{ route('products.show', $product->slug) }}" class="text-dark text-decoration-none"><h3 class="h6 fw-bold product-name">{{ $product->name }}</h3></a>
        <div class="d-flex justify-content-between align-items-center mt-auto gap-2 pt-2">
            <div><div class="product-price">{{ number_format($product->price, 0, ',', '.') }}₫</div><div class="tiny text-muted">{{ $product->stock > 0 ? 'Còn '.$product->stock.' sản phẩm' : 'Hết hàng' }}</div></div>
            @auth
                @if(!auth()->user()->isAdmin() && $product->stock > 0)
                    <form method="POST" action="{{ route('cart.add', $product) }}" class="m-0" data-ajax-cart>@csrf<input type="hidden" name="quantity" value="1"><button class="btn btn-primary btn-add" title="Thêm vào giỏ" aria-label="Thêm {{ $product->name }} vào giỏ">+</button></form>
                @endif
            @else
                @if($product->stock > 0)
                <a class="btn btn-primary btn-add" href="{{ route('login') }}" title="Đăng nhập để mua" aria-label="Đăng nhập để mua">+</a>
                @endif
            @endauth
        </div>
    </div>
</div>
