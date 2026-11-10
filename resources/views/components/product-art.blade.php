@props(['product'])
@php
    $symbol = match($product->category?->slug) {
        'ban-phim' => '⌨️',
        'chuot-may-tinh' => '🖱️',
        'am-thanh' => '🎧',
        default => '💻',
    };
@endphp
<div {{ $attributes->class(['product-art']) }}>
    @if($product->image_url)
        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy">
    @else
        <span aria-hidden="true" class="product-art-icon">{{ $symbol }}</span>
        <span class="product-art-label">OPENSHOP / TECH</span>
    @endif
</div>
